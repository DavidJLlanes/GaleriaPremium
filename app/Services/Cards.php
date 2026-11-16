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

    public static function values(): array
    {
        $fx = Setting::get('cards_reveal');
        $st = Setting::get('cards_title_style');
        $fx = Module::exists("reveals/$fx") ? $fx : self::REVEAL_DEFAULT;
        $st = Module::exists("card-titles/$st") ? $st : self::TITLE_DEFAULT;
        return [
            'reveal'    => $fx,
            'parallax'  => Setting::get('cards_parallax', '0') === '1',
            'titles'    => Setting::get('cards_titles', '1') !== '0',
            'style'     => $st,
            'style_opts' => ModuleOptions::values('card-titles', $st, self::PREFIX),
        ];
    }

    public static function save(array $post): void
    {
        $fx = (string) ($post['cards_reveal'] ?? ''); $st = (string) ($post['cards_title_style'] ?? '');
        Setting::set('cards_reveal', Module::exists("reveals/$fx") ? $fx : self::REVEAL_DEFAULT);
        Setting::set('cards_parallax', !empty($post['cards_parallax']) ? '1' : '0');
        Setting::set('cards_titles', !empty($post['cards_titles']) ? '1' : '0');
        Setting::set('cards_title_style', Module::exists("card-titles/$st") ? $st : self::TITLE_DEFAULT);
        foreach (array_keys(Module::all('card-titles')) as $name) ModuleOptions::save('card-titles', $name, self::PREFIX, (array) ($post['title_opt'][$name] ?? []));
    }
}
