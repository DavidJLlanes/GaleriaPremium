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

    /** Lado máximo con el que se trabaja: lo que sea mayor se reduce nada más leerlo (los iconos salen de ahí; el mayor es de 1024 px). */
    private const WORK_EDGE = 2048;
    private const MAX_PIXELS = 120_000_000;     // por encima, GD no puede ni abrir la imagen con memoria razonable

    /** Valida una imagen subida (jpeg/png/webp/gif) y la devuelve como GD, ya reducida si es grande. Lanza RuntimeException con mensaje legible. */
    public static function read(string $path, int $minSide = 0): \GdImage
    {
        $info = @getimagesize($path);
        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
            throw new \RuntimeException('Formato no válido. Usa PNG, JPG, WebP o GIF.');
        }
        if ($minSide && min($info[0], $info[1]) < $minSide) {
            throw new \RuntimeException("La imagen es demasiado pequeña (mínimo {$minSide}×{$minSide} px; ideal 512×512 o más).");
        }
        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            throw new \RuntimeException('La imagen es enorme (más de 120 megapíxeles). Redúcela un poco antes de subirla.');
        }
        @ini_set('memory_limit', '1024M');
        $img = @imagecreatefromstring((string) file_get_contents($path));
        if (!$img) throw new \RuntimeException('No se pudo leer la imagen (¿falta memoria en el servidor?).');
        return self::alpha(self::shrink($img));
    }

    /** Reduce una imagen grande a WORK_EDGE px de lado largo (con suavizado) conservando la transparencia. */
    private static function shrink(\GdImage $img): \GdImage
    {
        $w = imagesx($img); $h = imagesy($img); $m = max($w, $h);
        if ($m <= self::WORK_EDGE) return $img;
        $k = self::WORK_EDGE / $m; $nw = max(1, (int) round($w * $k)); $nh = max(1, (int) round($h * $k));
        $out = imagecreatetruecolor($nw, $nh);
        imagealphablending($out, false); imagesavealpha($out, true);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
        imagecopyresampled($out, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return $out;
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


    /** Quita los márgenes transparentes (y las bandas que dejaba el antiguo «encajar en cuadrado»). */
    private static function trim(\GdImage $img): \GdImage
    {
        $c = @imagecropauto($img, IMG_CROP_TRANSPARENT);
        return $c instanceof \GdImage ? self::alpha($c) : $img;
    }

    /** Analiza el borde: ['opaque' => ¿la imagen llega opaca hasta los bordes?, 'color' => color medio del borde, 'uniform' => ¿es un color liso?] */
    private static function edges(\GdImage $img): array
    {
        $w = imagesx($img); $h = imagesy($img); $n = 0; $op = 0; $rs = $gs = $bs = 0; $px = [];
        $steps = 96;
        for ($i = 0; $i < $steps; $i++) {
            $x = (int) round(($w - 1) * $i / ($steps - 1)); $y = (int) round(($h - 1) * $i / ($steps - 1));
            foreach ([[$x, 0], [$x, $h - 1], [0, $y], [$w - 1, $y]] as [$a, $b]) {
                $c = imagecolorat($img, $a, $b); $n++;
                if ((($c >> 24) & 0x7F) < 8) { $op++; $r = ($c >> 16) & 255; $g = ($c >> 8) & 255; $bl = $c & 255; $rs += $r; $gs += $g; $bs += $bl; $px[] = [$r, $g, $bl]; }
            }
        }
        if ($op < $n * .95 || !$px) return ['opaque' => false, 'color' => null, 'uniform' => false];
        $m = [$rs / $op, $gs / $op, $bs / $op]; $dev = 0;
        foreach ($px as $p) $dev += abs($p[0] - $m[0]) + abs($p[1] - $m[1]) + abs($p[2] - $m[2]);
        return ['opaque' => true, 'color' => sprintf('#%02x%02x%02x', (int) round($m[0]), (int) round($m[1]), (int) round($m[2])), 'uniform' => $dev / $op / 3 < 14];
    }

    /** Lienzo cuadrado a sangre: la imagen cubre todo el cuadrado (se recorta el sobrante, centrado). */
    private static function cover(\GdImage $src, int $size): \GdImage
    {
        $sw = imagesx($src); $sh = imagesy($src); $m = min($sw, $sh);
        $c = imagecreatetruecolor($size, $size);
        imagealphablending($c, false); imagesavealpha($c, true);
        imagecopyresampled($c, $src, 0, 0, (int) (($sw - $m) / 2), (int) (($sh - $m) / 2), $size, $size, $m, $m);
        return $c;
    }

    /**
     * Icono cuadrado de $size px. $safe = fracción que ocupa el dibujo cuando hay que dejar margen (iconos adaptativos de Android).
     * - Imagen opaca de color liso en el borde: se encaja entera sobre ese mismo color (sin bandas ni costuras).
     * - Imagen opaca con fondo variado (foto, degradado): se recorta a sangre.
     * - Imagen con transparencia (logotipo): se coloca sobre el color del tema.
     */
    private static function tile(\GdImage $art, array $e, int $size, float $safe, string $themeBg, bool $solid): \GdImage
    {
        if ($e['opaque'] && $e['uniform']) return self::square($art, $size, $e['color'], $safe);
        if ($e['opaque']) return self::cover($art, $size);   // el sistema solo recorta las esquinas: el fondo llega hasta el borde
        return self::square($art, $size, $solid ? $themeBg : null, $solid ? $safe : 1.0);
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

        $art = self::trim($icon); $e = self::edges($art);
        // «any» (192/512): sin bandas; el dibujo opaco ocupa todo el cuadrado, el logotipo transparente se queda transparente
        self::png(self::tile($art, $e, 192, 1.0, $bg, false), "$dir/icon-192.png");
        self::png(self::tile($art, $e, 512, 1.0, $bg, false), "$dir/icon-512.png");
        // Android adaptativo: el sistema recorta (círculo, squircle…); lo importante va en la zona segura (≈ 80 %)
        self::png(self::tile($art, $e, 512, $e['opaque'] ? .8 : .66, $bg, true), "$dir/maskable-512.png");
        // iOS pone sus propias esquinas y rellena la transparencia de negro: icono opaco y a sangre
        self::png(self::tile($art, $e, 180, $e['opaque'] ? 1.0 : .8, $bg, true), "$dir/apple-touch-icon.png");
        self::png(self::tile($art, $e, 150, $e['opaque'] ? 1.0 : .8, $bg, true), "$dir/mstile-150.png");

        $pngs = [];
        foreach ([16, 32, 48] as $s) {
            $im = self::square(self::trim($fav), $s); self::png($im, "$dir/favicon-$s.png"); $pngs[$s] = self::pngBytes($im);
        }
        file_put_contents("$dir/favicon.ico", self::ico($pngs));

        // Antes se copiaban a public/ (a veces sin permiso de escritura, y quedaban copias viejas): se eliminan, los <link> apuntan a /uploads/brand/
        $pub = dirname(rtrim((string) Config::get('uploads.dir'), '/'));
        @unlink("$pub/favicon.ico"); @unlink("$pub/apple-touch-icon.png");
        return true;
    }

    /** Guarda el icono principal subido y regenera todo. */
    public static function setIcon(string $tmp): void
    {
        $img = self::read($tmp, 192);
        $img = self::trim($img); $m = max(imagesx($img), imagesy($img));
        if ($m > 1024) $img = self::alpha(imagescale($img, (int) round(imagesx($img) * 1024 / $m), (int) round(imagesy($img) * 1024 / $m)));
        self::png($img, self::dir() . '/icon-source.png');
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
