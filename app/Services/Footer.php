<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Module;
use App\Models\{Gallery, Setting};

/**
 * Pie de página (Admin → Pie). Tres piezas intercambiables:
 *   diseño del pie   → modules/footers/<nombre>        (view.php recibe $f y $opts)
 *   «llegada» (cómo aparece / se visualiza al llegar al final) → modules/footer-reveals/<nombre>
 *   contenido y firma → ajustes comunes de SPEC (footer_<clave>)
 * Ajustes propios de cada módulo vía `options` (ModuleOptions): prefijos footer_opt y footer_ropt.
 * Vista previa en el panel: la web en un iframe con ?footer_preview=1&ft[...]=… (solo con sesión de administrador).
 */
final class Footer
{
    public const DEFAULTS = ['design' => 'linea', 'reveal' => 'subir'];
    public const SPEC = [
        'enabled'   => ['label' => 'Mostrar el pie de página', 'type' => 'bool', 'default' => '1'],
        'text'      => ['label' => 'Texto de copyright (vacío = © año + nombre; {year} = año actual)', 'type' => 'text', 'default' => ''],
        'tone'      => ['label' => 'Fondo del pie', 'type' => 'select', 'default' => 'web', 'choices' => ['web' => 'El de la web (transparente)', 'dark' => 'Oscuro', 'light' => 'Claro', 'accent' => 'Color de acento']],
        'tag'       => ['label' => 'Mostrar el eslogan', 'type' => 'bool', 'default' => '1'],
        'social'    => ['label' => 'Mostrar redes sociales', 'type' => 'bool', 'default' => '1'],
        'contact'   => ['label' => 'Mostrar «Contacto» (correo de Sitio)', 'type' => 'bool', 'default' => '1'],
        'galleries' => ['label' => 'Mostrar lista de galerías (en los diseños que la usan)', 'type' => 'bool', 'default' => '1'],
        'top'       => ['label' => 'Mostrar «Arriba ↑»', 'type' => 'bool', 'default' => '1'],
        'admin'     => ['label' => 'Mostrar el enlace «Admin»', 'type' => 'bool', 'default' => '1'],
        'sig_text'  => ['label' => 'Firma (vacío = sin firma)', 'type' => 'text', 'default' => ''],
        'sig_url'   => ['label' => 'Enlace de la firma (opcional)', 'type' => 'text', 'default' => ''],
        'sig_style' => ['label' => 'Estilo de la firma', 'type' => 'select', 'default' => 'plain', 'choices' => ['plain' => 'Normal', 'caps' => 'Mayúsculas espaciadas', 'italic' => 'Cursiva', 'accent' => 'Con color de acento']],
    ];
    private const QUERY = ['design' => 'design', 'reveal' => 'reveal'];

    public static function previewing(): bool { return isset($_GET['footer_preview']) && Auth::check(); }

    private static function pick(string $kind, ?string $v): string
    {
        $dir = $kind === 'design' ? 'footers' : 'footer-reveals';
        return $v !== null && preg_match('/^[a-z0-9-]+$/', $v) && Module::exists("$dir/$v") ? $v : self::DEFAULTS[$kind];
    }

    public static function values(): array
    {
        $q = self::previewing() && isset($_GET['ft']) && is_array($_GET['ft']) ? $_GET['ft'] : null;
        $saved = Setting::get('footer_saved', '') === '1';
        $out = [];
        foreach (self::SPEC as $k => $o) {
            if ($q !== null) $v = ModuleOptions::clean($o, $q[$k] ?? ($o['type'] === 'bool' ? '0' : null));
            else {
                $v = Setting::get("footer_$k", '');
                if (!$saved && $k === 'text') $v = Setting::get('site_footer_text', '');          // ajuste anterior (antes en Sitio)
                if (!$saved && $k === 'admin') $v = Setting::get('site_footer_admin', '1');
            }
            $out[$k] = $v === '' && $o['type'] !== 'text' ? (string) $o['default'] : (string) $v;
        }
        foreach (['design', 'reveal'] as $k) $out[$k] = self::pick($k, $q !== null ? (is_string($q[$k] ?? null) ? $q[$k] : '') : Setting::get("footer_$k", ''));
        $out['opts'] = ModuleOptions::values('footers', $out['design'], 'footer_opt');
        $out['ropts'] = ModuleOptions::values('footer-reveals', $out['reveal'], 'footer_ropt');
        if ($q !== null) foreach ([['opts', 'footers', 'fo'], ['ropts', 'footer-reveals', 'fr']] as [$key, $kind, $qk])
            if (isset($_GET[$qk][$out[$key === 'opts' ? 'design' : 'reveal']]) && is_array($_GET[$qk][$out[$key === 'opts' ? 'design' : 'reveal']]))
                foreach (ModuleOptions::spec($kind, $out[$key === 'opts' ? 'design' : 'reveal']) as $k => $o) $out[$key][$k] = ModuleOptions::clean($o, $_GET[$qk][$out[$key === 'opts' ? 'design' : 'reveal']][$k] ?? ($o['type'] === 'bool' ? '0' : null));
        if (self::previewing()) $out['enabled'] = '1';
        return $out;
    }

    /** Datos ya preparados para las vistas de los diseños. */
    public static function data(array $v): array
    {
        $links = $v['social'] === '1' ? Site::socials() : [];
        $mail = $v['contact'] === '1' ? Site::get('email') : '';
        $copy = $v['text'] !== '' ? str_replace('{year}', date('Y'), $v['text']) : '© ' . date('Y') . ' ' . \App\Core\Config::get('app.name');
        $url = trim($v['sig_url']);
        if ($url !== '' && !preg_match('#^(https?://|mailto:)#i', $url)) $url = 'https://' . ltrim($url, '/');
        if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL) && !str_starts_with($url, 'mailto:')) $url = '';
        return [
            'copy' => $copy, 'name' => Site::get('name'), 'tag' => $v['tag'] === '1' ? (string) \App\Core\Config::get('app.tagline') : '',
            'links' => $links, 'mail' => $mail, 'top' => $v['top'] === '1', 'admin' => $v['admin'] === '1',
            'galleries' => $v['galleries'] === '1' ? Gallery::published() : [],
            'sig' => $v['sig_text'] !== '' ? ['text' => $v['sig_text'], 'url' => $url, 'style' => $v['sig_style']] : null,
        ];
    }

    public static function save(array $raw, array $opts, array $ropts): void
    {
        foreach (self::SPEC as $k => $o) Setting::set("footer_$k", ModuleOptions::clean($o, $raw[$k] ?? null));
        foreach (['design', 'reveal'] as $k) Setting::set("footer_$k", self::pick($k, is_string($raw[$k] ?? null) ? $raw[$k] : ''));
        Setting::set('footer_saved', '1');
        foreach (array_keys(Module::all('footers')) as $n) ModuleOptions::save('footers', $n, 'footer_opt', (array) ($opts[$n] ?? []));
        foreach (array_keys(Module::all('footer-reveals')) as $n) ModuleOptions::save('footer-reveals', $n, 'footer_ropt', (array) ($ropts[$n] ?? []));
    }
}
