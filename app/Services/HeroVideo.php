<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Config;

/**
 * Vídeo de fondo del hero «video» (Admin → Portada). Se guarda tal cual (mp4/webm) en public/uploads/hero/.
 * Excepción documentada a la regla 9: un vídeo no puede ser WebP; la foto de respaldo del hero sí lo es.
 */
final class HeroVideo
{
    public const MAX_BYTES = 40 * 1024 * 1024;       // = upload_max_filesize de public/.user.ini
    public const NAME = '/^hero-[a-f0-9]{10}\.(mp4|webm)$/';

    private static function dir(): string { return rtrim((string) Config::get('uploads.dir'), '/') . '/hero'; }
    public static function url(string $file): string { return rtrim((string) Config::get('uploads.url'), '/') . '/hero/' . rawurlencode($file); }
    public static function exists(string $file): bool { return preg_match(self::NAME, $file) === 1 && is_file(self::dir() . '/' . $file); }
    public static function mime(string $file): string { return str_ends_with($file, '.webm') ? 'video/webm' : 'video/mp4'; }

    /** Valida (por la cabecera real del archivo, no por su extensión) y guarda el vídeo. Devuelve el nombre del archivo. */
    public static function store(array $f): string
    {
        $err = $f['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) throw new \RuntimeException('El vídeo supera el límite del servidor (40 MB).');
        if ($err !== UPLOAD_ERR_OK) throw new \RuntimeException('No se pudo subir el vídeo.');
        if ($f['size'] > self::MAX_BYTES) throw new \RuntimeException('El vídeo supera 40 MB. Comprímelo o acórtalo (basta con unos segundos en bucle).');
        $head = (string) file_get_contents($f['tmp_name'], false, null, 0, 12);
        $ext = match (true) {
            substr($head, 4, 4) === 'ftyp' => 'mp4',                    // mp4 / m4v / mov
            str_starts_with($head, "\x1A\x45\xDF\xA3") => 'webm',        // contenedor Matroska/WebM
            default => null,
        };
        if ($ext === null) throw new \RuntimeException('Formato no válido: sube un vídeo .mp4 o .webm.');
        if (!is_dir(self::dir()) && !@mkdir(self::dir(), 0755, true) && !is_dir(self::dir())) throw new \RuntimeException('No se pudo crear la carpeta de vídeos.');
        $name = 'hero-' . bin2hex(random_bytes(5)) . '.' . $ext;
        $dst = self::dir() . '/' . $name;
        if (!@move_uploaded_file($f['tmp_name'], $dst) && !@copy($f['tmp_name'], $dst)) throw new \RuntimeException('No se pudo guardar el vídeo.');
        @chmod($dst, 0644);
        return $name;
    }

    /** Borra los vídeos que ya no usa la portada (subidas sustituidas o quitadas). */
    public static function prune(string $keep): void
    {
        foreach (glob(self::dir() . '/hero-*') ?: [] as $f) if (basename($f) !== $keep) @unlink($f);
    }
}
