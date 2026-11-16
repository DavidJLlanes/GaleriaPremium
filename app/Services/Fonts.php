<?php
declare(strict_types=1);
namespace App\Services;

/**
 * Tipografías alojadas en local (public/fonts/<id>/*.woff2, catálogo en config/fonts.php).
 * Cada TIPO DE TEXTO (rol) tiene su fuente, elegida en Admin → Sitio → Tipografías (ajuste site_font_<rol>).
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
    public const CATS = ['serif' => 'Serif (clásicas)', 'sans' => 'Sans-serif (modernas)', 'display' => 'Display (titulares)', 'script' => 'Manuscritas'];

    private static ?array $catalog = null;

    public static function catalog(): array
    {
        return self::$catalog ??= require ROOT . '/config/fonts.php';
    }

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

    public static function url(string $id, string $file): string { return "/fonts/$id/$file"; }

    /** @font-face de una fuente (todas sus variantes). */
    public static function face(string $id): string
    {
        $f = self::catalog()[$id]; $fam = $f['label']; $css = '';
        $one = static fn (string $file, string $weight): string =>
            "@font-face{font-family:'$fam';font-style:normal;font-display:swap;font-weight:$weight;src:url(" . self::url($id, $file) . ") format('woff2')}";
        if (isset($f['variable'])) return $one($f['variable'], $f['range'] ?? '100 900');
        foreach ($f['static'] as $w => $file) $css .= $one($file, (string) $w);
        return $css;
    }

    /** Archivo a precargar de una fuente (el peso 400 o la variable). */
    public static function preloadFile(string $id): string
    {
        $f = self::catalog()[$id];
        return $f['variable'] ?? ($f['static'][400] ?? reset($f['static']));
    }

    /** Fuentes usadas y CSS completo (@font-face + variables :root). $defaults = true → ignora los ajustes (panel de admin). */
    public static function build(bool $defaults = false): array
    {
        $vars = ''; $used = [];
        foreach (self::ROLES as $role => $_) {
            $id = self::of($role, $defaults); $used[$id] = true;
            $vars .= "--f-$role:" . self::family($id) . ';';
        }
        $css = '';
        foreach (array_keys($used) as $id) $css .= self::face($id);
        return ['css' => $css . ':root{' . $vars . '}', 'preload' => array_unique([self::of('body', $defaults), self::of('hero', $defaults), self::of('brand', $defaults)])];
    }
}
