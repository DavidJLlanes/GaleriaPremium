<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Module;
use App\Models\Setting;

/**
 * Menú de galerías de la barra superior (Admin → Menú). Tres piezas intercambiables, cada una en su carpeta de módulos:
 *   icono («hamburguesa»)  → modules/menu-icons/<nombre>
 *   efecto de despliegue   → modules/menu-reveals/<nombre>
 *   diseño del menú        → modules/menu-styles/<nombre>
 * Los ítems son las galerías publicadas.
 */
final class SiteMenu
{
    public const KINDS = ['icon' => 'menu-icons', 'reveal' => 'menu-reveals', 'style' => 'menu-styles'];
    public const DEFAULTS = ['icon' => 'lineas', 'reveal' => 'cortina', 'style' => 'grande'];
    private const QUERY = ['icon' => 'mi', 'reveal' => 'mr', 'style' => 'ms'];

    /** Vista previa del panel: solo con sesión de administrador y ?menu_preview=1 (los ajustes se leen de la URL). */
    public static function previewing(): bool { return isset($_GET['menu_preview']) && Auth::check(); }

    public static function values(): array
    {
        $out = []; $opts = [];
        foreach (self::KINDS as $k => $dir) {
            $v = Setting::get("menu_$k");
            if (self::previewing() && isset($_GET[self::QUERY[$k]]) && is_string($_GET[self::QUERY[$k]])) $v = $_GET[self::QUERY[$k]];
            $v = preg_match('/^[a-z0-9-]+$/', $v) && Module::exists("$dir/$v") ? $v : self::DEFAULTS[$k];
            $out[$k] = $v;
            $opts[$k] = ModuleOptions::values($dir, $v, "menu_{$k}_opt");
        }
        return $out + ['opts' => $opts];
    }

    public static function save(array $post): void
    {
        foreach (self::KINDS as $k => $dir) {
            $v = (string) ($post["menu_$k"] ?? '');
            Setting::set("menu_$k", preg_match('/^[a-z0-9-]+$/', $v) && Module::exists("$dir/$v") ? $v : self::DEFAULTS[$k]);
            foreach (array_keys(Module::all($dir)) as $name) ModuleOptions::save($dir, $name, "menu_{$k}_opt", (array) ($post["opt_$k"][$name] ?? []));
        }
    }
}
