<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\{Database, Response};
use App\Models\User;

final class Auth
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW = 600; // 10 min

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        session_name('wsid');
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Strict', 'secure' => $https]);
        session_start();
    }

    public static function check(): bool
    {
        self::start();
        return !empty($_SESSION['uid']);
    }

    /** Protege una ruta: redirige al login (o 401 JSON en endpoints AJAX). */
    public static function guard(bool $json = false): void
    {
        if (self::check()) return;
        if ($json) Response::json(['ok' => false, 'error' => 'Sesión caducada. Recarga la página.'], 401);
        Response::redirect('/admin/login');
    }

    public static function blocked(): bool
    {
        $st = Database::pdo()->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND at > ?');
        $st->execute([self::ip(), time() - self::WINDOW]);
        return (int) $st->fetchColumn() >= self::MAX_ATTEMPTS;
    }

    public static function attempt(string $username, string $password): bool
    {
        self::start();
        $user = User::findByUsername(trim($username));
        // password_verify también con hash falso, para no revelar si el usuario existe por tiempo
        $hash = $user['password_hash'] ?? '$2y$10$usesomesillystringforsalt.abcdefghijklmnopqrstuvwxyz012';
        if ($user && password_verify($password, $hash)) {
            self::signIn($user);
            return true;
        }
        self::recordFailure();
        return false;
    }

    /** Inicia la sesión de un usuario ya verificado (contraseña o passkey). */
    public static function signIn(array $user): void
    {
        self::start();
        Database::pdo()->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute([self::ip()]);
        session_regenerate_id(true);
        $_SESSION['uid'] = (int) $user['id'];
        $_SESSION['user'] = $user['username'];
    }

    public static function recordFailure(): void
    {
        Database::pdo()->prepare('INSERT INTO login_attempts (ip, at) VALUES (?, ?)')->execute([self::ip(), time()]);
        Database::pdo()->prepare('DELETE FROM login_attempts WHERE at < ?')->execute([time() - 86400]);   // la tabla no crece sin fin
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function userId(): int { self::start(); return (int) ($_SESSION['uid'] ?? 0); }

    public static function username(): string { self::start(); return (string) ($_SESSION['user'] ?? ''); }

    private static function ip(): string { return $_SERVER['REMOTE_ADDR'] ?? 'cli'; }
}
