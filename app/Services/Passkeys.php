<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\{Config, Database};

/**
 * Passkeys (WebAuthn) sin dependencias: entrar con huella, Face ID, Windows Hello o llave de seguridad.
 * Solo se guarda la clave pública; la verificación (reto, origen, rpId, UP+UV, firma, contador) vive aquí.
 */
final class Passkeys
{
    private const TTL = 300;   // el reto caduca a los 5 min

    public static function b64e(string $s): string { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }
    public static function b64d(string $s): string { return (string) base64_decode(strtr($s, '-_', '+/') . str_repeat('=', (4 - strlen($s) % 4) % 4), true); }

    public static function rpId(): string { return strtolower((string) preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'))); }

    public static function origin(): string
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        return ($https ? 'https' : 'http') . '://' . strtolower((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    }

    private static function challenge(string $purpose, ?int $uid = null): string
    {
        Auth::start();
        $c = random_bytes(32);
        $_SESSION['pk'] = ['p' => $purpose, 'c' => self::b64e($c), 'u' => $uid, 't' => time()];
        return self::b64e($c);
    }

    /** Recupera y consume el reto (un solo uso). */
    private static function take(string $purpose): ?array
    {
        Auth::start();
        $s = $_SESSION['pk'] ?? null; unset($_SESSION['pk']);
        return is_array($s) && $s['p'] === $purpose && time() - (int) $s['t'] <= self::TTL ? $s : null;
    }

    public static function loginOptions(): array
    {
        return ['challenge' => self::challenge('get'), 'rpId' => self::rpId(), 'timeout' => 60000, 'userVerification' => 'required'];
    }

    public static function registerOptions(int $uid, string $username): array
    {
        $ex = array_map(fn ($r) => $r['cred_id'], Database::all('SELECT cred_id FROM passkeys WHERE user_id = ?', [$uid]));
        return [
            'challenge' => self::challenge('create', $uid),
            'rp' => ['name' => (string) Config::get('app.name'), 'id' => self::rpId()],
            'user' => ['id' => self::b64e('u' . $uid), 'name' => $username, 'displayName' => $username],
            'pubKeyCredParams' => [['type' => 'public-key', 'alg' => -7], ['type' => 'public-key', 'alg' => -257]],
            'authenticatorSelection' => ['residentKey' => 'required', 'requireResidentKey' => true, 'userVerification' => 'required'],
            'excludeCredentials' => $ex, 'attestation' => 'none', 'timeout' => 60000,
        ];
    }

    private static function clientData(string $json, string $type, array $ch): ?string
    {
        $d = json_decode($json, true);
        if (!is_array($d) || ($d['type'] ?? '') !== $type) return 'Respuesta no válida.';
        if (!hash_equals($ch['c'], (string) ($d['challenge'] ?? ''))) return 'El reto no coincide. Inténtalo de nuevo.';
        if (($d['origin'] ?? '') !== self::origin()) return 'El origen no coincide con esta web.';
        if (!empty($d['crossOrigin'])) return 'Origen cruzado no permitido.';
        return null;
    }

    /** Guarda una credencial nueva del usuario con sesión iniciada. Devuelve un error o null. */
    public static function register(int $uid, array $in): ?string
    {
        $ch = self::take('create');
        if (!$ch || (int) $ch['u'] !== $uid) return 'La petición caducó. Inténtalo de nuevo.';
        $cd = self::b64d((string) ($in['clientDataJSON'] ?? ''));
        if ($e = self::clientData($cd, 'webauthn.create', $ch)) return $e;
        $id = (string) ($in['id'] ?? ''); $alg = (int) ($in['alg'] ?? 0);
        $der = self::b64d((string) ($in['publicKey'] ?? ''));
        if ($id === '' || strlen($id) > 1400 || $der === '' || !in_array($alg, [-7, -257], true)) return 'Este dispositivo usa un tipo de clave no admitido.';
        $pem = "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
        $k = @openssl_pkey_get_public($pem);
        $type = $k ? (openssl_pkey_get_details($k)['type'] ?? -1) : -1;
        if (($alg === -7 && $type !== OPENSSL_KEYTYPE_EC) || ($alg === -257 && $type !== OPENSSL_KEYTYPE_RSA)) return 'La clave pública no es válida.';
        if (Database::one('SELECT id FROM passkeys WHERE cred_id = ?', [$id])) return 'Este dispositivo ya está registrado.';
        $name = trim((string) preg_replace('/[\x00-\x1F\x7F<>]/u', '', (string) ($in['name'] ?? '')));
        $name = mb_substr($name !== '' ? $name : 'Mi dispositivo', 0, 40);
        Database::pdo()->prepare('INSERT INTO passkeys (user_id, cred_id, public_key, alg, name, created_at) VALUES (?,?,?,?,?,?)')
            ->execute([$uid, $id, $pem, $alg, $name, time()]);
        return null;
    }

    /** Verifica el acceso con passkey e inicia la sesión. Devuelve un error o null. */
    public static function login(array $in): ?string
    {
        if (Auth::blocked()) return 'Demasiados intentos. Espera 10 minutos.';
        $err = self::verify($in, $row);
        if ($err) { Auth::recordFailure(); return $err; }
        $user = Database::one('SELECT * FROM users WHERE id = ?', [(int) $row['user_id']]);
        if (!$user) { Auth::recordFailure(); return 'No se pudo entrar.'; }
        Database::pdo()->prepare('UPDATE passkeys SET last_used = ? WHERE id = ?')->execute([time(), (int) $row['id']]);
        Auth::signIn($user);
        return null;
    }

    private static function verify(array $in, ?array &$row): ?string
    {
        $ch = self::take('get');
        if (!$ch) return 'La petición caducó. Inténtalo de nuevo.';
        $cd = self::b64d((string) ($in['clientDataJSON'] ?? ''));
        if ($e = self::clientData($cd, 'webauthn.get', $ch)) return $e;
        $row = Database::one('SELECT * FROM passkeys WHERE cred_id = ?', [(string) ($in['id'] ?? '')]);
        if (!$row) return 'Este dispositivo no está registrado.';
        $ad = self::b64d((string) ($in['authenticatorData'] ?? '')); $sig = self::b64d((string) ($in['signature'] ?? ''));
        if (strlen($ad) < 37 || $sig === '') return 'Respuesta no válida.';
        if (!hash_equals(hash('sha256', self::rpId(), true), substr($ad, 0, 32))) return 'El dominio no coincide.';
        $flags = ord($ad[32]);
        if (!($flags & 0x01) || !($flags & 0x04)) return 'Hace falta tu huella, Face ID o PIN.';
        $ok = openssl_verify($ad . hash('sha256', $cd, true), $sig, $row['public_key'], OPENSSL_ALGO_SHA256);
        if ($ok !== 1) return 'La firma no es válida.';
        $n = unpack('N', substr($ad, 33, 4))[1];
        if (($n > 0 || (int) $row['counter'] > 0) && $n <= (int) $row['counter']) return 'Credencial clonada o repetida.';
        Database::pdo()->prepare('UPDATE passkeys SET counter = ? WHERE id = ?')->execute([$n, (int) $row['id']]);
        return null;
    }

    public static function all(int $uid): array
    {
        return Database::all('SELECT id, name, created_at, last_used FROM passkeys WHERE user_id = ? ORDER BY id', [$uid]);
    }

    public static function delete(int $uid, int $id): void
    {
        Database::pdo()->prepare('DELETE FROM passkeys WHERE id = ? AND user_id = ?')->execute([$id, $uid]);
    }
}
