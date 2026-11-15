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
}
