<?php
declare(strict_types=1);
namespace App\Services\Brand;

use App\Core\Config;
use App\Services\Site;

/**
 * Genera TODOS los iconos de la webapp a partir de una sola imagen:
 *   favicon.ico (16/32/48) y PNG 16/32/48 · apple-touch-icon 180 (iPhone/iPad)
 *   Android/Chrome 192 y 512 · maskable 512 (iconos adaptativos de Android) · mstile 150 (Windows)
 * Los iconos son PNG (lo exigen iOS/Android/Windows); las FOTOS siguen siendo WebP 75 %.
 */
final class IconBuilder
{
    public static function dir(): string
    {
        $d = rtrim((string) Config::get('uploads.dir'), '/') . '/brand';
        if (!is_dir($d)) @mkdir($d, 0775, true);
        return $d;
    }

    /** Valida una imagen subida (jpeg/png/webp/gif) y la devuelve como GD. Lanza RuntimeException con mensaje legible. */
    public static function read(string $path, int $minSide = 0): \GdImage
    {
        $info = @getimagesize($path);
        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
            throw new \RuntimeException('Formato no válido. Usa PNG, JPG, WebP o GIF.');
        }
        if ($minSide && min($info[0], $info[1]) < $minSide) {
            throw new \RuntimeException("La imagen es demasiado pequeña (mínimo {$minSide}×{$minSide} px; ideal 512×512 o más).");
        }
        $img = @imagecreatefromstring((string) file_get_contents($path));
        if (!$img) throw new \RuntimeException('No se pudo leer la imagen.');
        return self::alpha($img);
    }

    private static function alpha(\GdImage $img): \GdImage
    {
        $w = imagesx($img); $h = imagesy($img);
        $out = imagecreatetruecolor($w, $h);
        imagealphablending($out, false); imagesavealpha($out, true);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
        imagecopy($out, $img, 0, 0, 0, 0, $w, $h);
        return $out;
    }

    /** Lienzo cuadrado $size: la imagen cabe entera ($scale = fracción ocupada), sobre $bg (hex) o transparente. */
    public static function square(\GdImage $src, int $size, ?string $bg = null, float $scale = 1.0): \GdImage
    {
        $c = imagecreatetruecolor($size, $size);
        imagealphablending($c, false); imagesavealpha($c, true);
        if ($bg) { [$r, $g, $b] = sscanf($bg, '#%02x%02x%02x'); imagefill($c, 0, 0, imagecolorallocate($c, $r, $g, $b)); }
        else imagefill($c, 0, 0, imagecolorallocatealpha($c, 0, 0, 0, 127));
        imagealphablending($c, true);
        $sw = imagesx($src); $sh = imagesy($src);
        $k = ($size * $scale) / max($sw, $sh);
        $dw = max(1, (int) round($sw * $k)); $dh = max(1, (int) round($sh * $k));
        imagecopyresampled($c, $src, (int) (($size - $dw) / 2), (int) (($size - $dh) / 2), 0, 0, $dw, $dh, $sw, $sh);
        imagealphablending($c, false);
        return $c;
    }

    private static function png(\GdImage $img, string $file): void { imagepng($img, $file, 9); }

    private static function pngBytes(\GdImage $img): string { ob_start(); imagepng($img, null, 9); return (string) ob_get_clean(); }

    /** favicon.ico con imágenes PNG incrustadas (válido en todos los navegadores actuales). */
    private static function ico(array $pngs): string
    {
        $n = count($pngs); $out = pack('vvv', 0, 1, $n); $off = 6 + 16 * $n; $data = '';
        foreach ($pngs as $size => $bytes) {
            $out .= pack('CCCCvvVV', $size >= 256 ? 0 : $size, $size >= 256 ? 0 : $size, 0, 0, 1, 32, strlen($bytes), $off);
            $off += strlen($bytes); $data .= $bytes;
        }
        return $out . $data;
    }

    /** Reconstruye todos los iconos desde las fuentes guardadas (icon-source.png y, opcional, favicon-source.png). */
    public static function rebuild(): bool
    {
        $dir = self::dir(); $iconSrc = "$dir/icon-source.png";
        if (!is_file($iconSrc)) return false;
        $bg = Site::get('theme');
        $icon = self::read($iconSrc);
        $fav = is_file("$dir/favicon-source.png") ? self::read("$dir/favicon-source.png") : $icon;

        self::png(self::square($icon, 192), "$dir/icon-192.png");
        self::png(self::square($icon, 512), "$dir/icon-512.png");
        self::png(self::square($icon, 512, $bg, .62), "$dir/maskable-512.png");      // zona segura de Android (≈ 80 % del círculo)
        self::png(self::square($icon, 180, $bg, .84), "$dir/apple-touch-icon.png");   // iOS rellena la transparencia con negro: se aplana
        self::png(self::square($icon, 150, $bg, .8), "$dir/mstile-150.png");

        $pngs = [];
        foreach ([16, 32, 48] as $s) {
            $im = self::square($fav, $s); self::png($im, "$dir/favicon-$s.png"); $pngs[$s] = self::pngBytes($im);
        }
        file_put_contents("$dir/favicon.ico", self::ico($pngs));

        // Muchos navegadores piden /favicon.ico y /apple-touch-icon.png en la raíz: copias físicas en public/
        $pub = dirname(rtrim((string) Config::get('uploads.dir'), '/'));
        @copy("$dir/favicon.ico", "$pub/favicon.ico");
        @copy("$dir/apple-touch-icon.png", "$pub/apple-touch-icon.png");
        return true;
    }

    /** Guarda el icono principal subido y regenera todo. */
    public static function setIcon(string $tmp): void
    {
        $img = self::read($tmp, 192);
        self::png(self::square($img, min(1024, max(imagesx($img), imagesy($img)))), self::dir() . '/icon-source.png');
        self::rebuild();
    }

    /** Favicon propio (opcional): solo afecta a las pestañas del navegador. */
    public static function setFavicon(string $tmp): void
    {
        $img = self::read($tmp, 32);
        self::png(self::square($img, 256), self::dir() . '/favicon-source.png');
        self::rebuild();
    }

    /** Logo: WebP sin pérdida (conserva transparencia y nitidez), ancho máx. 800 px. */
    public static function setLogo(string $tmp): void
    {
        $img = self::read($tmp);
        $w = imagesx($img); $h = imagesy($img);
        if ($w > 800) { $img = imagescale($img, 800, (int) round($h * 800 / $w)); imagealphablending($img, false); imagesavealpha($img, true); }
        imagewebp($img, self::dir() . '/logo.webp', defined('IMG_WEBP_LOSSLESS') ? IMG_WEBP_LOSSLESS : 100);
    }

    public static function clear(string $what): void
    {
        $dir = self::dir(); $pub = dirname(rtrim((string) Config::get('uploads.dir'), '/'));
        $files = match ($what) {
            'logo'    => ['logo.webp'],
            'favicon' => ['favicon-source.png'],
            'icon'    => ['icon-source.png', 'icon-192.png', 'icon-512.png', 'maskable-512.png', 'apple-touch-icon.png', 'mstile-150.png', 'favicon-source.png', 'favicon-16.png', 'favicon-32.png', 'favicon-48.png', 'favicon.ico'],
            default   => [],
        };
        foreach ($files as $f) @unlink("$dir/$f");
        if ($what === 'icon') { @unlink("$pub/favicon.ico"); @unlink("$pub/apple-touch-icon.png"); }
        if ($what === 'favicon') self::rebuild();
    }
}
