<?php
declare(strict_types=1);
namespace App\Services;

/**
 * Matemática de recortes. Un recorte son 4 fracciones de la imagen original: x, y, w, h (0..1).
 * "Proporción" = ancho/alto en PÍXELES: (w·ancho) / (h·alto).
 */
final class Crop
{
    public static function ratio(array $c, int $iw, int $ih): float
    {
        return ($c['w'] * $iw) / max(1e-6, $c['h'] * $ih);
    }

    /** Recorte más grande posible con proporción $ratio, centrado en $prev (o en el centro de la foto). */
    public static function fit(float $ratio, int $iw, int $ih, ?array $prev = null): array
    {
        $ratio = max(0.2, min(5.0, $ratio));
        if ($iw / max(1, $ih) >= $ratio) { $h = 1.0; $w = $ratio * $ih / $iw; }
        else                              { $w = 1.0; $h = $iw / ($ratio * $ih); }
        $cx = $prev ? $prev['x'] + $prev['w'] / 2 : .5; $cy = $prev ? $prev['y'] + $prev['h'] / 2 : .5;
        return [
            'x' => max(0.0, min(1 - $w, $cx - $w / 2)), 'y' => max(0.0, min(1 - $h, $cy - $h / 2)),
            'w' => $w, 'h' => $h,
        ];
    }

    /** Recorte válido (dentro de la foto y con tamaño mínimo) o null. */
    public static function sanitize(array $in): ?array
    {
        $c = ['x' => (float) ($in['x'] ?? 0), 'y' => (float) ($in['y'] ?? 0), 'w' => (float) ($in['w'] ?? 0), 'h' => (float) ($in['h'] ?? 0)];
        if ($c['w'] < .02 || $c['h'] < .02) return null;
        $c['w'] = min(1.0, $c['w']); $c['h'] = min(1.0, $c['h']);
        $c['x'] = max(0.0, min(1 - $c['w'], $c['x'])); $c['y'] = max(0.0, min(1 - $c['h'], $c['y']));
        return $c;
    }

    /** Fuerza la proporción $ratio manteniendo el ancho (y si no cabe, reduce). */
    public static function lock(array $c, float $ratio, int $iw, int $ih): array
    {
        $h = $c['w'] * $iw / ($ratio * $ih);
        if ($h > 1) { $h = 1.0; $c['w'] = $ratio * $ih / $iw; }
        $c['h'] = $h;
        $c['x'] = max(0.0, min(1 - $c['w'], $c['x'])); $c['y'] = max(0.0, min(1 - $c['h'], $c['y']));
        return $c;
    }

    public static function of(array $photo): ?array
    {
        if ($photo['crop_w'] === null || $photo['crop_h'] === null) return null;
        return ['x' => (float) $photo['crop_x'], 'y' => (float) $photo['crop_y'], 'w' => (float) $photo['crop_w'], 'h' => (float) $photo['crop_h']];
    }
}
