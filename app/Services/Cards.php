<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Module;
use App\Models\Setting;

/**
 * Aspecto de las tarjetas de galería de la página principal (Admin → Tarjetas):
 * efecto de aparición al hacer scroll (modules/reveals/<efecto>), títulos sí/no y su diseño (modules/card-titles/<diseño>).
 */
final class Cards
{
    public const REVEAL_DEFAULT = 'fade-up';
    public const TITLE_DEFAULT = 'classic';
    public const PREFIX = 'card_title_opt';
    public const LAYOUT_DEFAULT = 'rejilla';
    public const LAYOUT_PREFIX = 'cards_layout_opt';

    /** Vista previa del panel: ?cards_preview=1&cl=<diseño> (solo con sesión de administrador). */
    public static function previewing(): bool { return isset($_GET['cards_preview']) && Auth::check(); }

    public static function layout(?string $v = null): string
    {
        $v ??= self::previewing() && is_string($_GET['cl'] ?? null) ? $_GET['cl'] : Setting::get('cards_layout', '');
        return preg_match('/^[a-z0-9-]+$/', $v) && Module::exists("home-layouts/$v") ? $v : self::LAYOUT_DEFAULT;
    }

    public static function values(): array
    {
        $fx = Setting::get('cards_reveal');
        $st = Setting::get('cards_title_style');
        $fx = Module::exists("reveals/$fx") ? $fx : self::REVEAL_DEFAULT;
        $st = Module::exists("card-titles/$st") ? $st : self::TITLE_DEFAULT;
        $ly = self::layout();
        return [
            'layout'    => $ly,
            'layout_opts' => ModuleOptions::values('home-layouts', $ly, self::LAYOUT_PREFIX),
            'reveal'    => $fx,
            'parallax'  => Setting::get('cards_parallax', '0') === '1',
            'titles'    => Setting::get('cards_titles', '1') !== '0',
            'style'     => $st,
            'style_opts' => ModuleOptions::values('card-titles', $st, self::PREFIX),
            'font'      => ($f = Setting::get('cards_title_font')) !== '' && isset(Fonts::catalog()[$f]) ? $f : '',       // '' = la de Sitio → Tipografías
            'weight'    => isset(Fonts::WEIGHTS[(int) Setting::get('cards_title_weight')]) ? (int) Setting::get('cards_title_weight') : 0,   // 0 = la del diseño
            'italic'    => Setting::get('cards_title_italic', '0') === '1',
            'size'      => max(50, min(180, (int) (Setting::get('cards_title_size') ?: 100))),                              // % del tamaño del diseño
            'color_on'  => Setting::get('cards_title_color_on', '0') === '1',
            'color'     => preg_match('/^#[0-9a-fA-F]{6}$/', $col = Setting::get('cards_title_color')) ? $col : '#f2efe9',
        ];
    }

    /** Variables CSS del contenedor de tarjetas: sustituyen localmente la tipografía de los títulos, su tamaño y su color. */
    public static function vars(array $c): string
    {
        $o = '--ct-s:' . $c['size'] / 100 . ';';
        if ($c['font'] !== '') $o .= '--f-title:' . Fonts::family($c['font']) . ';';
        if ($c['weight']) $o .= '--w-title:' . $c['weight'] . ';';
        if ($c['italic']) $o .= '--st-title:italic;';
        if ($c['color_on']) $o .= '--ct-c:' . $c['color'] . ';';
        return $o;
    }

    /** @font-face de la fuente elegida (en la web pública solo esa). */
    public static function face(array $c): string { return $c['font'] !== '' ? Fonts::face($c['font']) : ''; }

    public static function save(array $post): void
    {
        $fx = (string) ($post['cards_reveal'] ?? ''); $st = (string) ($post['cards_title_style'] ?? '');
        Setting::set('cards_reveal', Module::exists("reveals/$fx") ? $fx : self::REVEAL_DEFAULT);
        Setting::set('cards_layout', self::layout((string) ($post['cards_layout'] ?? '')));
        foreach (array_keys(Module::all('home-layouts')) as $name) ModuleOptions::save('home-layouts', $name, self::LAYOUT_PREFIX, (array) ($post['layout_opt'][$name] ?? []));
        Setting::set('cards_parallax', !empty($post['cards_parallax']) ? '1' : '0');
        Setting::set('cards_titles', !empty($post['cards_titles']) ? '1' : '0');
        Setting::set('cards_title_style', Module::exists("card-titles/$st") ? $st : self::TITLE_DEFAULT);
        $f = (string) ($post['cards_title_font'] ?? ''); $w = (int) ($post['cards_title_weight'] ?? 0); $col = (string) ($post['cards_title_color'] ?? '');
        Setting::set('cards_title_font', isset(Fonts::catalog()[$f]) ? $f : '');
        Setting::set('cards_title_weight', isset(Fonts::WEIGHTS[$w]) ? (string) $w : '');
        Setting::set('cards_title_italic', !empty($post['cards_title_italic']) ? '1' : '0');
        Setting::set('cards_title_size', (string) max(50, min(180, (int) ($post['cards_title_size'] ?? 100))));
        Setting::set('cards_title_color_on', !empty($post['cards_title_color_on']) ? '1' : '0');
        Setting::set('cards_title_color', preg_match('/^#[0-9a-fA-F]{6}$/', $col) ? $col : '#f2efe9');
        foreach (array_keys(Module::all('card-titles')) as $name) ModuleOptions::save('card-titles', $name, self::PREFIX, (array) ($post['title_opt'][$name] ?? []));
    }
}
