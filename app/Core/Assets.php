<?php
declare(strict_types=1);
namespace App\Core;

/** Registro de CSS/JS por módulo. Solo se cargan los de los módulos usados en la página. */
final class Assets
{
    private static array $css = [];
    private static array $js  = [];

    public static function addCss(string $id): void { self::$css[$id] = true; }
    public static function addJs(string $id): void  { self::$js[$id]  = true; }

    private static function url(string $id, string $file): string
    {
        $v = @filemtime(ROOT . "/public/assets/m/$id/$file") ?: @filemtime(Module::path($id) . "/$file") ?: 0;
        return "/assets/m/$id/$file?v=$v";
    }

    public static function css(): string
    {
        $o = '';
        foreach (array_keys(self::$css) as $id) $o .= '<link rel="stylesheet" href="' . e(self::url($id, 'style.css')) . '">' . "\n";
        return $o;
    }

    public static function js(): string
    {
        $o = '';
        foreach (array_keys(self::$js) as $id) $o .= '<script src="' . e(self::url($id, 'script.js')) . '" defer></script>' . "\n";
        return $o;
    }

    /** Sustituye los marcadores del layout por las etiquetas reales. */
    public static function inject(string $html): string
    {
        return str_replace(['<!--assets:css-->', '<!--assets:js-->'], [self::css(), self::js()], $html);
    }
}
