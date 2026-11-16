<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Module;
use App\Models\Setting;

/**
 * Pantalla de entrada breve (Admin → Entrada). Cada diseño es un módulo aislado en modules/intros/<nombre>/
 * (view.php recibe $text, $sub, $opts; sus ajustes propios van en `options` de su module.php, prefijo intro_opt).
 * Ajustes comunes guardados como intro_<clave>. La orquesta components/intro. Solo se muestra en la portada.
 * Vista previa en el panel: la propia web en un iframe con ?intro_preview=1&in[...]=… (solo con sesión de administrador).
 */
final class Intro
{
    public const DEFAULT = 'luz';
    private const PREFIX = 'intro_opt';

    public const SPEC = [
        'enabled' => ['label' => 'Mostrar pantalla de entrada', 'type' => 'bool', 'default' => '0'],
        'text'    => ['label' => 'Texto principal (vacío = nombre de la web)', 'type' => 'text', 'default' => ''],
        'sub'     => ['label' => 'Texto pequeño (vacío = eslogan de la web)', 'type' => 'text', 'default' => ''],
        'dur'     => ['label' => 'Duración aproximada (segundos)', 'type' => 'range', 'min' => 2, 'max' => 8, 'step' => 0.5, 'default' => 3.5],
        'freq'    => ['label' => 'Cuándo se muestra', 'type' => 'select', 'default' => 'session', 'choices' => ['session' => 'Una vez por visita (sesión)', 'day' => 'Una vez al día', 'always' => 'Siempre que se abre la portada']],
        'exit'    => ['label' => 'Cómo desaparece', 'type' => 'select', 'default' => 'fade', 'choices' => ['fade' => 'Fundido', 'up' => 'Sube y deja ver la web', 'zoom' => 'Se agranda y se disuelve'],
            'hint' => 'El diseño «Cortina» se abre en dos y no usa esta opción.'],
        'bg'      => ['label' => 'Color de fondo', 'type' => 'color', 'default' => '#050506'],
        'fg'      => ['label' => 'Color del texto', 'type' => 'color', 'default' => '#f2efe9'],
        'accent'  => ['label' => 'Color de acento (luz, línea, contador)', 'type' => 'color', 'default' => '#ffb45a'],
        'skip'    => ['label' => 'Permitir saltarla con un clic o una tecla', 'type' => 'bool', 'default' => '1'],
    ];

    public static function previewing(): bool { return isset($_GET['intro_preview']) && Auth::check(); }

    public static function design(?string $v = null): string
    {
        $v ??= Setting::get('intro_design', '');
        return preg_match('/^[a-z0-9-]+$/', $v) && Module::exists("intros/$v") ? $v : self::DEFAULT;
    }

    /** Ajustes actuales (con los de la vista previa si se está previsualizando). */
    public static function values(): array
    {
        $q = self::previewing() && isset($_GET['in']) && is_array($_GET['in']) ? $_GET['in'] : null;
        $out = [];
        foreach (self::SPEC as $k => $o) {
            $v = $q !== null ? ModuleOptions::clean($o, $q[$k] ?? ($o['type'] === 'bool' ? '0' : null)) : Setting::get("intro_$k", '');
            $out[$k] = $v === '' && $o['type'] !== 'text' ? (string) $o['default'] : (string) $v;
        }
        $out['design'] = self::design($q !== null ? (is_string($q['design'] ?? null) ? $q['design'] : '') : null);
        $out['opts'] = self::options($out['design']);
        if ($q !== null && isset($_GET['in_opt'][$out['design']]) && is_array($_GET['in_opt'][$out['design']]))
            foreach (ModuleOptions::spec('intros', $out['design']) as $k => $o) $out['opts'][$k] = ModuleOptions::clean($o, $_GET['in_opt'][$out['design']][$k] ?? ($o['type'] === 'bool' ? '0' : null));
        if (self::previewing()) { $out['enabled'] = '1'; $out['freq'] = 'always'; }
        if ($out['text'] === '') $out['text'] = Site::get('name');
        if ($out['sub'] === '') $out['sub'] = Site::get('tagline');
        return $out;
    }

    public static function options(string $design): array { return ModuleOptions::values('intros', $design, self::PREFIX); }

    public static function save(array $raw, array $opts): void
    {
        foreach (self::SPEC as $k => $o) Setting::set("intro_$k", ModuleOptions::clean($o, $raw[$k] ?? null));
        Setting::set('intro_design', self::design(is_string($raw['design'] ?? null) ? $raw['design'] : ''));
        foreach (array_keys(Module::all('intros')) as $name) ModuleOptions::save('intros', $name, self::PREFIX, (array) ($opts[$name] ?? []));
    }
}
