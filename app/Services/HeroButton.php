<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Setting;

/** Ajustes del botón de la portada (comunes a todos los estilos de hero). Se editan en Admin → Portada. */
final class HeroButton
{
    public const STYLES = [
        'outline' => 'Contorno (pastilla)', 'solid' => 'Relleno claro', 'gold' => 'Dorado (color de acento)', 'glass' => 'Cristal',
        'square' => 'Rectangular fino', 'line' => 'Solo texto subrayado', 'arrow' => 'Texto con flecha',
    ];
    public const SIZES = [1 => 'Muy pequeño', 2 => 'Pequeño', 3 => 'Mediano', 4 => 'Grande', 5 => 'Muy grande'];

    public static function values(): array
    {
        $style = Setting::get('hero_btn_style');
        $size  = (int) Setting::get('hero_btn_size');
        $down  = Setting::get('hero_btn_down');
        return [
            'style' => isset(self::STYLES[$style]) ? $style : 'outline',
            'size'  => isset(self::SIZES[$size]) ? $size : 3,
            'down'  => $down === '' ? 56 : max(0, min(240, (int) $down)),
        ];
    }

    public static function scale(int $size): float { return [1 => .8, 2 => .9, 3 => 1.0, 4 => 1.2, 5 => 1.45][$size] ?? 1.0; }
}
