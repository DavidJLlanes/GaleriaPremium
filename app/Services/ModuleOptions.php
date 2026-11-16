<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Module;
use App\Models\Setting;

/**
 * Ajustes propios de un módulo (cualquier tipo: card-titles, reveals…), declarados en su module.php:
 *   'options' => ['clave' => ['label' => ..., 'type' => color|range|select|bool, 'default' => ..., ...]]
 * Se guardan como <prefijo>_<módulo>_<clave> (p. ej. card_title_opt_luz_speed). HeroOptions hace lo mismo para los heroes.
 */
final class ModuleOptions
{
    public static function spec(string $kind, string $name): array { return Module::exists("$kind/$name") ? (Module::meta("$kind/$name")['options'] ?? []) : []; }

    public static function values(string $kind, string $name, string $prefix): array
    {
        $out = [];
        foreach (self::spec($kind, $name) as $k => $o) {
            $v = Setting::get("{$prefix}_{$name}_$k", '');
            $out[$k] = $v === '' ? (string) ($o['default'] ?? '') : $v;
        }
        return $out;
    }

    /** $raw = $_POST[...][$name] ?? []. */
    public static function save(string $kind, string $name, string $prefix, array $raw): void
    {
        foreach (self::spec($kind, $name) as $k => $o) Setting::set("{$prefix}_{$name}_$k", self::clean($o, $raw[$k] ?? null));
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
