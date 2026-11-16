<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Module;
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

    public const CURSOR_DEFAULT = 'aro';
    private const CURSOR_PREFIX = 'fx_cursor_opt';

    public static function values(): array
    {
        $out = [];
        foreach (self::SPEC as $k => $o) { $v = Setting::get("fx_$k", ''); $out[$k] = $v === '' ? (string) $o['default'] : $v; }
        $c = Setting::get('fx_cursor', '');
        $c = $c === '0' ? 'off' : ($c === '' || $c === '1' ? self::CURSOR_DEFAULT : $c);   // '1'/'0' = ajuste antiguo (sí/no)
        $out['cursor'] = $c === 'off' || (preg_match('/^[a-z0-9-]+$/', $c) && Module::exists("cursors/$c")) ? $c : self::CURSOR_DEFAULT;
        return $out;
    }

    /** Opciones propias del diseño de cursor elegido (color, longitud…). */
    public static function cursorOptions(string $design): array { return $design === 'off' ? [] : ModuleOptions::values('cursors', $design, self::CURSOR_PREFIX); }

    public static function save(array $raw, array $cursorOpts = []): void
    {
        foreach (self::SPEC as $k => $o) Setting::set("fx_$k", ModuleOptions::clean($o, $raw[$k] ?? null));
        $c = (string) ($raw['cursor'] ?? '');
        Setting::set('fx_cursor', $c === 'off' || (preg_match('/^[a-z0-9-]+$/', $c) && Module::exists("cursors/$c")) ? $c : self::CURSOR_DEFAULT);
        foreach (array_keys(Module::all('cursors')) as $name) ModuleOptions::save('cursors', $name, self::CURSOR_PREFIX, (array) ($cursorOpts[$name] ?? []));
    }
}
