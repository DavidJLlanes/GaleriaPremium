<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Module;
use App\Models\Setting;

/**
 * Ajustes propios de cada hero (Admin → Portada → "Ajustes de este diseño").
 * Cada hero los declara en su module.php:  'options' => ['clave' => ['label' => ..., 'type' => color|range|select|bool, 'default' => ..., ...]]
 * Se guardan como hero_opt_<hero>_<clave> y llegan a la vista del hero en $opts.
 */
final class HeroOptions
{
    public static function spec(string $type): array { return Module::exists("heroes/$type") ? (Module::meta("heroes/$type")['options'] ?? []) : []; }

    /** Valores guardados sobre los de por defecto. */
    public static function values(string $type): array
    {
        $out = [];
        foreach (self::spec($type) as $k => $o) {
            $v = Setting::get("hero_opt_{$type}_$k", '');
            $out[$k] = $v === '' ? (string) ($o['default'] ?? '') : $v;
        }
        return $out;
    }

    /** $raw = $_POST['hero_opt'][$type] ?? []. */
    public static function save(string $type, array $raw): void
    {
        foreach (self::spec($type) as $k => $o) Setting::set("hero_opt_{$type}_$k", self::clean($o, $raw[$k] ?? null));
    }

    private static function clean(array $o, mixed $v): string
    {
        $def = (string) ($o['default'] ?? '');
        $v = is_scalar($v) ? trim((string) $v) : '';
        return match ($o['type'] ?? 'text') {
            'bool'   => $v !== '' && $v !== '0' ? '1' : '0',
            'color'  => preg_match('/^#[0-9a-fA-F]{6}$/', $v) ? $v : $def,
            'range'  => (string) max((float) $o['min'], min((float) $o['max'], is_numeric($v) ? (float) $v : (float) $def)),
            'select' => isset($o['choices'][$v]) ? $v : $def,
            default  => mb_substr($v, 0, 80),
        };
    }
}
