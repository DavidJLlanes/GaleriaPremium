<?php
declare(strict_types=1);
namespace App\Services;

/**
 * Tipografías alojadas en local (public/fonts/<id>/*.woff2, catálogo en config/fonts.php).
 * Cada TIPO DE TEXTO (rol) tiene su fuente, elegida en Admin → Sitio → Tipografías (ajuste site_font_<rol>).
 * Además de la fuente, cada rol puede tener su grosor (ajuste site_fontw_<rol>) y cursiva (site_fonti_<rol>).
 * Las fuentes propias subidas desde el panel (App\Services\FontLibrary) se mezclan con el catálogo.
 * Solo se emite el @font-face de las fuentes que realmente se usan, y se precargan las críticas.
 */
final class Fonts
{
    /** rol => [etiqueta, descripción, fuente por defecto, texto de muestra] */
    public const ROLES = [
        'brand'   => ['Nombre de la web',      'El nombre en la cabecera (si no usas solo logo).', 'fraunces', 'LUX AETERNA'],
        'hero'    => ['Título de la portada',  'El título grande de la portada.',                  'fraunces', 'Fotografía que se siente'],
        'title'   => ['Títulos de galerías',   'Títulos de galería, tarjetas y fotos destacadas.', 'fraunces', 'Otoño en León'],
        'menu'    => ['Menú y etiquetas',      'Menú, textos pequeños en mayúsculas, contadores.',  'inter',    'GALERÍAS · PORTFOLIO'],
        'body'    => ['Texto normal',          'Descripciones, párrafos y pie de página.',          'inter',    'Hojas, paseos y luz cálida de noviembre en el parque.'],
        'caption' => ['Pies de foto',          'El nombre de cada foto y su pie al ampliarla.',     'fraunces', 'Paseo de plátanos'],
        'button'  => ['Botones',               'Botones de la web pública.',                        'inter',    'Explorar galerías'],
    ];
    public const CATS = ['serif' => 'Serif (clásicas)', 'sans' => 'Sans-serif (modernas)', 'display' => 'Display (titulares)', 'script' => 'Manuscritas', 'mono' => 'Monoespaciadas', 'custom' => 'Mis fuentes (subidas)'];
    public const WEIGHTS = [100 => 'Fina', 200 => 'Extra ligera', 300 => 'Ligera', 400 => 'Normal', 500 => 'Media', 600 => 'Seminegrita', 700 => 'Negrita', 800 => 'Extranegrita', 900 => 'Negra'];

    private static ?array $catalog = null;

    /** Catálogo del tema (config/fonts.php) + fuentes propias subidas. */
    public static function catalog(): array
    {
        return self::$catalog ??= (require ROOT . '/config/fonts.php') + FontLibrary::catalog();
    }

    /** Fuerza a releer las fuentes propias (tras subir o borrar). */
    public static function reset(): void { self::$catalog = null; }

    /** Fuente elegida para un rol (id del catálogo). */
    public static function of(string $role, bool $defaults = false): string
    {
        $def = self::ROLES[$role][2];
        if ($defaults) return $def;
        $id = Site::get("font_$role");
        return isset(self::catalog()[$id]) ? $id : $def;
    }

    public static function family(string $id): string
    {
        $f = self::catalog()[$id];
        return "'" . $f['label'] . "'," . $f['fallback'];
    }

    /** Todas las variantes de una fuente: [file, weight ('400' o '100 900'), italic, base URL, formato]. */
    public static function faces(string $id): array
    {
        $f = self::catalog()[$id];
        if (isset($f['faces'])) return $f['faces'];                       // fuente propia
        $out = [];
        foreach (['n' => false, 'i' => true] as $k => $italic) {
            if (!isset($f[$k])) continue;
            $o = $f[$k];
            if (isset($o['var'])) $out[] = ['file' => $o['var'], 'weight' => $o['range'], 'italic' => $italic, 'base' => "/fonts/$id", 'fmt' => 'woff2'];
            else foreach ($o['static'] as $w => $file) $out[] = ['file' => $file, 'weight' => (string) $w, 'italic' => $italic, 'base' => "/fonts/$id", 'fmt' => 'woff2'];
        }
        return $out;
    }

    /** Pesos (100…900) que ofrece la fuente en estilo normal. */
    public static function weights(string $id): array
    {
        $ws = [];
        foreach (self::faces($id) as $fc) {
            if ($fc['italic']) continue;
            [$a, $b] = array_map('intval', explode(' ', $fc['weight'] . ' ' . $fc['weight']));
            foreach (array_keys(self::WEIGHTS) as $w) if ($w >= $a && $w <= $b) $ws[$w] = true;
        }
        $ws = array_keys($ws); sort($ws);
        return $ws ?: [400];
    }

    public static function hasItalic(string $id): bool
    {
        foreach (self::faces($id) as $fc) if ($fc['italic']) return true;
        return false;
    }

    /** Grosor elegido para un rol (null = el de por defecto del diseño). Se ajusta al peso disponible más cercano. */
    public static function weight(string $role, bool $defaults = false): ?int
    {
        if ($defaults) return null;
        $v = (int) Site::get("fontw_$role");
        if (!isset(self::WEIGHTS[$v])) return null;
        $best = null;
        foreach (self::weights(self::of($role)) as $w) if ($best === null || abs($w - $v) < abs($best - $v)) $best = $w;
        return $best;
    }

    public static function italic(string $role, bool $defaults = false): bool
    {
        return !$defaults && Site::on("fonti_$role") && self::hasItalic(self::of($role));
    }

    /** Datos de una fuente para la vista previa del panel. */
    public static function meta(string $id): array
    {
        return ['fam' => self::family($id), 'w' => self::weights($id), 'i' => self::hasItalic($id), 'css' => self::face($id)];
    }

    /** @font-face de una fuente (todas sus variantes). */
    public static function face(string $id): string
    {
        $fam = str_replace("'", "\\'", self::catalog()[$id]['label']); $css = '';
        foreach (self::faces($id) as $fc)
            $css .= "@font-face{font-family:'$fam';font-style:" . ($fc['italic'] ? 'italic' : 'normal') . ";font-display:swap;font-weight:{$fc['weight']};src:url(" . $fc['base'] . '/' . rawurlencode($fc['file']) . ") format('{$fc['fmt']}')}";
        return $css;
    }

    /** Archivo woff2 a precargar (estilo normal, peso 400 o la variable); null si no hay woff2. */
    public static function preloadUrl(string $id): ?string
    {
        $best = null;
        foreach (self::faces($id) as $fc) {
            if ($fc['italic'] || $fc['fmt'] !== 'woff2') continue;
            $dist = abs((int) explode(' ', $fc['weight'])[0] - 400);
            if ($best === null || $dist < $best[0]) $best = [$dist, $fc['base'] . '/' . rawurlencode($fc['file'])];
        }
        return $best[1] ?? null;
    }

    /** Fuentes usadas y CSS completo (@font-face + variables :root). $defaults = true → ignora los ajustes (panel de admin). */
    public static function build(bool $defaults = false): array
    {
        $vars = ''; $used = [];
        foreach (self::ROLES as $role => $_) {
            $id = self::of($role, $defaults); $used[$id] = true;
            $vars .= "--f-$role:" . self::family($id) . ';';
            if (($w = self::weight($role, $defaults)) !== null) $vars .= "--w-$role:$w;";
            if (self::italic($role, $defaults)) $vars .= "--st-$role:italic;";
        }
        $css = '';
        foreach (array_keys($used) as $id) $css .= self::face($id);
        $pre = array_filter(array_map(fn ($r) => self::preloadUrl(self::of($r, $defaults)), ['body', 'hero', 'brand']));
        return ['css' => $css . ':root{' . $vars . '}', 'preload' => array_values(array_unique($pre))];
    }
}
