<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Config;

/**
 * Único punto de conversión de imágenes del proyecto.
 * REGLA: toda foto (original, miniatura, subida, demo) se guarda en WebP calidad 75.
 */
final class ImageProcessor
{
    public static function quality(): int { return (int) Config::get('image.quality', 75); }

    /** Carga cualquier imagen soportada por GD aplicando la orientación EXIF. */
    private static function load(string $path): \GdImage
    {
        $img = @imagecreatefromstring((string) file_get_contents($path));
        if (!$img) throw new \RuntimeException("Imagen no válida: $path");

        if (function_exists('exif_read_data') && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg'], true)) {
            $o = @exif_read_data($path)['Orientation'] ?? 1;
            $img = match ((int) $o) {
                3 => imagerotate($img, 180, 0) ?: $img,
                6 => imagerotate($img, -90, 0) ?: $img,
                8 => imagerotate($img, 90, 0) ?: $img,
                default => $img,
            };
        }
        return $img;
    }

    /**
     * Convierte $src a WebP en $dst (lado mayor <= $maxEdge). Devuelve [ancho, alto].
     */
    public static function convert(string $src, string $dst, ?int $maxEdge = null): array
    {
        $img = self::load($src);
        $maxEdge ??= (int) Config::get('image.max_edge', 2400);
        $w = imagesx($img); $h = imagesy($img);
        if (max($w, $h) > $maxEdge) {
            $scale = $maxEdge / max($w, $h);
            $img = imagescale($img, (int) round($w * $scale), (int) round($h * $scale), IMG_BICUBIC);
        }
        imagepalettetotruecolor($img);
        imagewebp($img, $dst, self::quality());
        return [imagesx($img), imagesy($img)];
    }

    /** Miniatura WebP de ancho $width. */
    public static function thumbnail(string $src, string $dst, ?int $width = null): void
    {
        $img = self::load($src);
        $width ??= (int) Config::get('uploads.thumb_w', 800);
        if (imagesx($img) > $width) $img = imagescale($img, $width, -1, IMG_BICUBIC);
        imagepalettetotruecolor($img);
        imagewebp($img, $dst, self::quality());
    }

    /** Miniatura minúscula (data URI WebP, calidad 75) para el efecto «desenfoque a nítido» mientras carga la foto real. */
    public static function placeholder(string $src, int $width = 28): string
    {
        $img = self::load($src);
        $img = imagescale($img, $width, -1, IMG_BILINEAR_FIXED) ?: $img;
        imagepalettetotruecolor($img);
        ob_start(); imagewebp($img, null, self::quality()); $bin = (string) ob_get_clean();
        return 'data:image/webp;base64,' . base64_encode($bin);
    }

    /** Datos de cámara de la foto ORIGINAL subida (el WebP final no conserva EXIF). Nunca se guarda el GPS. */
    public static function exif(string $path): array
    {
        if (!function_exists('exif_read_data')) return [];
        $e = @exif_read_data($path, 'EXIF,IFD0', false);
        if (!is_array($e)) return [];
        $frac = static function ($v): ?float { if (is_numeric($v)) return (float) $v; if (is_string($v) && preg_match('~^(-?\d+(?:\.\d+)?)/(\d+(?:\.\d+)?)$~', $v, $m) && (float) $m[2] > 0) return (float) $m[1] / (float) $m[2]; return null; };
        $out = [];
        $make = trim((string) ($e['Make'] ?? '')); $model = trim((string) ($e['Model'] ?? ''));
        if ($model !== '') $out['cam'] = ($make !== '' && stripos($model, $make) === false ? "$make " : '') . $model;
        if (($t = $frac($e['ExposureTime'] ?? null)) && $t > 0) $out['exp'] = $t >= 1 ? rtrim(rtrim(number_format($t, 1), '0'), '.') . ' s' : '1/' . (int) round(1 / $t) . ' s';
        if (($f = $frac($e['FNumber'] ?? null)) && $f > 0) $out['f'] = 'f/' . rtrim(rtrim(number_format($f, 1), '0'), '.');
        if (!empty($e['ISOSpeedRatings'])) $out['iso'] = 'ISO ' . (is_array($e['ISOSpeedRatings']) ? $e['ISOSpeedRatings'][0] : $e['ISOSpeedRatings']);
        if (($m = $frac($e['FocalLength'] ?? null)) && $m > 0) $out['mm'] = round($m) . ' mm';
        $d = (string) ($e['DateTimeOriginal'] ?? ''); if (preg_match('~^(\d{4}):(\d{2}):(\d{2})~', $d, $m) && (int) $m[1] > 1990) $out['when'] = "$m[3]/$m[2]/$m[1]";
        return $out;
    }
}
