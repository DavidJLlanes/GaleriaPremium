<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Setting;

/**
 * Efectos globales de «sensación premium» (Admin → Efectos). Cada efecto es un módulo aislado en modules/components/:
 * smooth-scroll, cursor, grain, sound, page-transition y photo-load. Ajustes guardados como fx_<clave>.
 * Para añadir uno: una entrada en SPEC y su módulo (se carga desde layouts/main).
 */
final class Effects
{
    public const SPEC = [
        'smooth' => ['label' => 'Scroll con inercia', 'type' => 'select', 'default' => 'medium', 'choices' => ['off' => 'Desactivado', 'soft' => 'Suave', 'medium' => 'Medio', 'strong' => 'Intenso'],
            'hint' => 'La página se desliza con peso y se frena despacio. Solo con ratón o trackpad en ordenador; en el móvil se mantiene el scroll nativo.'],
        'cursor' => ['label' => 'Cursor propio (solo ordenador)', 'type' => 'bool', 'default' => '1',
            'hint' => 'Un aro fino sigue al ratón, crece sobre las fotos con una etiqueta y atrae suavemente a los botones.'],
        'cursor_label' => ['label' => 'Texto del cursor sobre las fotos', 'type' => 'text', 'default' => 'Ver'],
        'grain' => ['label' => 'Grano de película', 'type' => 'select', 'default' => 'subtle', 'choices' => ['off' => 'Sin grano', 'subtle' => 'Sutil', 'medium' => 'Medio'],
            'hint' => 'Una textura casi imperceptible sobre los fondos oscuros que evita el negro plano de pantalla.'],
        'transition' => ['label' => 'Transiciones entre páginas', 'type' => 'bool', 'default' => '1',
            'hint' => 'Fundido suave al cambiar de página y la portada de la galería que se transforma en su cabecera. Navegadores recientes (Chrome, Edge, Safari 18+); en el resto, cambio normal.'],
        'lqip' => ['label' => 'Fotos con «desenfoque a nítido» al cargar', 'type' => 'bool', 'default' => '1',
            'hint' => 'Cada foto aparece primero como una miniatura difuminada de su color y se enfoca al llegar.'],
        'sound' => ['label' => 'Sonidos suaves (clic al abrir menú y cambiar de foto)', 'type' => 'bool', 'default' => '0',
            'hint' => 'Sonidos sintetizados, muy discretos. Desactivado por defecto.'],
    ];

    public static function values(): array
    {
        $out = [];
        foreach (self::SPEC as $k => $o) { $v = Setting::get("fx_$k", ''); $out[$k] = $v === '' ? (string) $o['default'] : $v; }
        return $out;
    }

    public static function save(array $raw): void
    {
        foreach (self::SPEC as $k => $o) Setting::set("fx_$k", ModuleOptions::clean($o, $raw[$k] ?? null));
    }
}
