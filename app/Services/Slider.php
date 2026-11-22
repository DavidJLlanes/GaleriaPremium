<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\{Database, Module};
use App\Models\{Gallery, Photo};

/**
 * Sliders de la portada (Admin → Sliders). Un slider es una fila de `galleries` con kind = 'slider': así reutiliza fotos, subida, recorte
 * (la proporción la fija la PRIMERA foto subida y queda fija), orden, destacada/fijada y lightbox. Su configuración vive en options.slider.
 * Piezas intercambiables, cada una en su carpeta: diseño modules/sliders/<n>, transición modules/slider-fx/<n>,
 * flechas modules/slider-arrows/<n> y puntos guía modules/slider-dots/<n>.
 */
final class Slider
{
    public const SPEC = [
        'design'   => ['label' => 'Diseño', 'type' => 'module', 'kind' => 'sliders', 'default' => 'clasico'],
        'fx'       => ['label' => 'Transición', 'type' => 'module', 'kind' => 'slider-fx', 'default' => 'fade'],
        'arrows'   => ['label' => 'Flechas', 'type' => 'module', 'kind' => 'slider-arrows', 'default' => 'circulo', 'none' => 'Sin flechas'],
        'dots'     => ['label' => 'Puntos guía', 'type' => 'module', 'kind' => 'slider-dots', 'default' => 'puntos', 'none' => 'Sin puntos guía'],
        'autoplay' => ['label' => 'Pasar las fotos automáticamente', 'type' => 'bool', 'default' => '1'],
        'interval' => ['label' => 'Segundos entre fotos', 'type' => 'range', 'min' => 2, 'max' => 15, 'step' => 0.5, 'default' => '5'],
        'loop'     => ['label' => 'Volver a la primera al llegar al final', 'type' => 'bool', 'default' => '1'],
        'hover'    => ['label' => 'Pausar al pasar el cursor', 'type' => 'bool', 'default' => '1'],
        'captions' => ['label' => 'Mostrar el nombre de cada foto', 'type' => 'bool', 'default' => '1'],
        'title'    => ['label' => 'Mostrar el título del slider encima', 'type' => 'bool', 'default' => '0'],
    ];

    public static function defaults(): array { $o = []; foreach (self::SPEC as $k => $s) $o[$k] = (string) $s['default']; return $o; }

    /** Piezas disponibles de cada tipo (Module::all, con respaldo si falta la carpeta). */
    public static function modules(string $kind): array { return Module::all($kind); }

    /** Configuración válida de un slider (valores guardados + por defecto; lo desconocido vuelve al valor por defecto). */
    public static function config(array $g): array
    {
        $saved = (json_decode((string) ($g['options'] ?? '{}'), true) ?: [])['slider'] ?? [];
        $out = [];
        foreach (self::SPEC as $k => $s) $out[$k] = self::clean($k, $saved[$k] ?? $s['default']);
        return $out;
    }

    private static function clean(string $k, $v): string
    {
        $s = self::SPEC[$k]; $v = is_string($v) || is_numeric($v) ? (string) $v : (string) $s['default'];
        return match ($s['type']) {
            'bool'   => $v === '1' ? '1' : '0',
            'range'  => (string) max($s['min'], min($s['max'], (float) str_replace(',', '.', $v))),
            'module' => (($s['none'] ?? null) && $v === 'none') ? 'none' : (preg_match('/^[a-z0-9-]+$/', $v) && Module::exists($s['kind'] . '/' . $v) ? $v : self::firstModule($s)),
            default  => $v,
        };
    }

    private static function firstModule(array $s): string { return Module::exists($s['kind'] . '/' . $s['default']) ? (string) $s['default'] : (string) (array_key_first(Module::all($s['kind'])) ?? ''); }

    public static function save(int $id, array $post): void
    {
        $g = Gallery::find($id); if (!$g || $g['kind'] !== 'slider') return;
        $all = json_decode((string) ($g['options'] ?? '{}'), true) ?: [];
        $cfg = [];
        foreach (self::SPEC as $k => $s) $cfg[$k] = self::clean($k, $post['sl_' . $k] ?? ($s['type'] === 'bool' ? '0' : $s['default']));
        $all['slider'] = $cfg;
        Gallery::saveOptions($id, $all);
    }

    /** Sliders para el panel (con portada y nº de fotos). */
    public static function all(): array
    {
        return Pinned::apply(Database::all(
            "SELECT g.*, (SELECT COUNT(*) FROM photos WHERE gallery_id = g.id) AS photo_count,
                    (SELECT filename FROM photos WHERE gallery_id = g.id ORDER BY sort_order, id LIMIT 1) AS cover
             FROM galleries g WHERE g.kind = 'slider' ORDER BY g.sort_order, g.id"
        ), 's');
    }

    /** Atributos comunes del contenedor .sl (los lee components/slider-engine). $mode = stack | track | coverflow. */
    public static function attrs(array $d, string $mode, string $design): string
    {
        $c = $d['cfg'];
        return sprintf('class="sl sl--%s" data-slider data-mode="%s" data-fx="%s" data-auto="%s" data-int="%s" data-loop="%s" data-hover="%s" data-show-exif="%s" style="--r:%s" tabindex="0" role="region" aria-roledescription="carrusel" aria-label="%s"',
            e($design), e($mode), e($c['fx']), $c['autoplay'], e($c['interval']), $c['loop'], $c['hover'], Gallery::showExif($d['g']) ? '1' : '0', round($d['ratio'], 5), e($d['g']['title']));
    }

    /** Datos listos para pintar un slider en la web. */
    public static function forView(array $g): array
    {
        return ['g' => $g, 'cfg' => self::config($g), 'photos' => Photo::forGallery((int) $g['id']), 'ratio' => (float) ($g['crop_ratio'] ?? 0) ?: 1.5];
    }
}
