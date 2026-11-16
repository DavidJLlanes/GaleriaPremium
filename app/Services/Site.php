<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\{Config, Database};
use App\Models\Setting;

/**
 * Ajustes de identidad y comportamiento de la web (nombre, marca, colores, webapp, redes...).
 * Se guardan en la tabla settings con el prefijo "site_". Para añadir una opción: una línea en FIELDS.
 */
final class Site
{
    public const LANGS = ['es' => 'Español', 'en' => 'English', 'ca' => 'Català', 'gl' => 'Galego', 'eu' => 'Euskara', 'fr' => 'Français', 'pt' => 'Português', 'de' => 'Deutsch', 'it' => 'Italiano'];

    /** Ajustes de la barra superior: se editan en Admin → Barra y menú (el resto, en Admin → Sitio). */
    public const BAR_KEYS = ['header_home', 'header_pages', 'brand_mode', 'hdr_name_on', 'hdr_name_color', 'hdr_menu_on', 'hdr_menu_color', 'hdr_logo_on', 'hdr_logo_color'];

    /** clave => [tipo, valor por defecto, opciones]. Tipos: text, url, color, bool, enum, font, weight. */
    public const FIELDS = [
        'name'         => ['text',  ''],
        'short_name'   => ['text',  ''],
        'tagline'      => ['text',  ''],
        'description'  => ['text',  ''],
        'og_text'      => ['text',  ''],
        'lang'         => ['enum',  'es', ['es', 'en', 'ca', 'gl', 'eu', 'fr', 'pt', 'de', 'it']],
        'accent'       => ['color', '#d8b36a'],
        'theme'        => ['color', '#0a0a0b'],
        'bg'           => ['color', '#0a0a0b'],
        'brand_mode'   => ['enum',  'text', ['text', 'logo', 'both']],
        'header_home'  => ['bool',  '1'],
        'header_pages' => ['bool',  '1'],
        // Colores de la barra superior (cada uno solo se aplica si su casilla está activada)
        'hdr_name_on'  => ['bool',  '0'], 'hdr_name_color' => ['color', '#f2efe9'],
        'hdr_menu_on'  => ['bool',  '0'], 'hdr_menu_color' => ['color', '#f2efe9'],
        'hdr_logo_on'  => ['bool',  '0'], 'hdr_logo_color' => ['color', '#f2efe9'],
        'display'      => ['enum',  'standalone', ['standalone', 'fullscreen', 'minimal-ui', 'browser']],
        'noindex'      => ['bool',  '0'],
        'email'        => ['text',  ''],
        'instagram'    => ['handle',   ''],
        'x'            => ['handle',   ''],
        'facebook'     => ['handle',   ''],
        'threads'      => ['handle',   ''],
        'youtube'      => ['handle',   ''],
        'linkedin'     => ['handle',   ''],
        'tiktok'       => ['handle',   ''],
        // Tipografía por tipo de texto ('' = la de por defecto). Ver App\Services\Fonts::ROLES
        'font_brand'   => ['font',  ''],
        'font_hero'    => ['font',  ''],
        'font_title'   => ['font',  ''],
        'font_menu'    => ['font',  ''],
        'font_body'    => ['font',  ''],
        'font_caption' => ['font',  ''],
        'font_button'  => ['font',  ''],
        'font_herobtn' => ['font',  ''],
        'font_intro'   => ['font',  ''],
        'font_footer'  => ['font',  ''],
        // Grosor (100-900, '' = el del diseño) y cursiva por tipo de texto
        'fontw_brand' => ['weight', ''], 'fonti_brand' => ['bool', '0'],
        'fontw_hero' => ['weight', ''], 'fonti_hero' => ['bool', '0'],
        'fontw_title' => ['weight', ''], 'fonti_title' => ['bool', '0'],
        'fontw_menu' => ['weight', ''], 'fonti_menu' => ['bool', '0'],
        'fontw_body' => ['weight', ''], 'fonti_body' => ['bool', '0'],
        'fontw_caption' => ['weight', ''], 'fonti_caption' => ['bool', '0'],
        'fontw_button' => ['weight', ''], 'fonti_button' => ['bool', '0'],
        'fontw_herobtn' => ['weight', ''], 'fonti_herobtn' => ['bool', '0'],
        'fontw_intro' => ['weight', ''], 'fonti_intro' => ['bool', '0'],
        'fontw_footer' => ['weight', ''], 'fonti_footer' => ['bool', '0'],
    ];
    public const SOCIALS = ['instagram' => 'Instagram', 'x' => 'X', 'facebook' => 'Facebook', 'threads' => 'Threads', 'youtube' => 'YouTube', 'linkedin' => 'LinkedIn', 'tiktok' => 'TikTok'];

    /** Dirección de cada red a partir del usuario (sin @). */
    public const SOCIAL_URL = [
        'instagram' => 'https://www.instagram.com/%s', 'x' => 'https://x.com/%s', 'facebook' => 'https://www.facebook.com/%s',
        'threads' => 'https://www.threads.net/@%s', 'youtube' => 'https://www.youtube.com/@%s', 'linkedin' => 'https://www.linkedin.com/in/%s',
        'tiktok' => 'https://www.tiktok.com/@%s',
    ];
    /** Lo que se muestra delante del usuario en el formulario (la web completa el resto). */
    public const SOCIAL_PREFIX = [
        'instagram' => 'instagram.com/', 'x' => 'x.com/', 'facebook' => 'facebook.com/', 'threads' => 'threads.net/@',
        'youtube' => 'youtube.com/@', 'linkedin' => 'linkedin.com/in/', 'tiktok' => 'tiktok.com/@',
    ];

    private static ?array $cache = null;

    private static function load(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (Database::all("SELECT key, value FROM settings WHERE key LIKE 'site\\_%' ESCAPE '\\'") as $r) self::$cache[substr($r['key'], 5)] = $r['value'];
        }
        return self::$cache;
    }

    public static function get(string $key): string
    {
        $def = self::FIELDS[$key][1] ?? '';
        if ($key === 'name')        $def = (string) Config::get('app.name', 'WarpStudio');
        if ($key === 'tagline')     $def = (string) Config::get('app.tagline', '');
        $v = self::load()[$key] ?? null;
        if ($v === null) return $def;
        if ($v === '' && in_array($key, ['name', 'lang', 'accent', 'theme', 'bg', 'display', 'brand_mode'], true)) return $def;
        return $v;
    }

    public static function on(string $key): bool { return self::get($key) === '1'; }

    public static function shortName(): string { return self::get('short_name') ?: mb_substr(self::get('name'), 0, 12); }

    /** Deja solo el usuario: quita la @, y si se pega una dirección completa extrae el usuario de ella. */
    public static function cleanHandle(string $key, string $v): string
    {
        $v = trim($v);
        if (preg_match('#^(https?://|www\.)#i', $v) || preg_match('#^[a-z0-9.-]+\.[a-z]{2,}/#i', $v)) {
            $path = (string) parse_url(preg_match('#^https?://#i', $v) ? $v : 'https://' . $v, PHP_URL_PATH);
            $parts = array_values(array_filter(explode('/', $path), 'strlen'));
            if ($key === 'linkedin' && isset($parts[1]) && in_array($parts[0], ['in', 'company', 'school'], true)) $v = ($parts[0] === 'in' ? '' : $parts[0] . '/') . $parts[1];
            else $v = $parts[0] ?? '';
        }
        $v = ltrim($v, '@ ');
        $allowed = $key === 'linkedin' ? '/[^A-Za-z0-9._\-\/%]/' : '/[^A-Za-z0-9._\-]/';
        return mb_substr(preg_replace($allowed, '', $v), 0, 80);
    }

    /** Usuario de una red tal como está guardado (acepta también valores antiguos con la dirección completa). */
    public static function socialHandle(string $key): string { return self::cleanHandle($key, self::get($key)); }

    /** Dirección completa de una red ('' si no hay usuario). */
    public static function socialUrl(string $key): string
    {
        $h = self::socialHandle($key); if ($h === '' || !isset(self::SOCIAL_URL[$key])) return '';
        if ($key === 'linkedin' && str_contains($h, '/')) return 'https://www.linkedin.com/' . $h;
        return sprintf(self::SOCIAL_URL[$key], $h);
    }

    /** Redes con usuario: [['key','label','url']] en el orden de SOCIALS. */
    public static function socials(): array
    {
        $out = [];
        foreach (self::SOCIALS as $k => $label) if (($u = self::socialUrl($k)) !== '') $out[] = ['key' => $k, 'label' => $label, 'url' => $u];
        return $out;
    }

    /** Valida y guarda $_POST según FIELDS. Los checkboxes ausentes se guardan como 0. $bar = false: todo menos la barra superior (Sitio); true: solo la barra superior (Barra y menú). */
    public static function save(array $post, bool $bar = false): void
    {
        foreach (self::FIELDS as $k => $f) {
            if (in_array($k, self::BAR_KEYS, true) !== $bar) continue;
            $type = $f[0]; $raw = $post["site_$k"] ?? null;
            if ($type === 'bool') { $v = $raw ? '1' : '0'; }
            else {
                $v = trim((string) $raw);
                if ($type === 'color' && !preg_match('/^#[0-9a-fA-F]{6}$/', $v)) $v = $f[1];
                if ($type === 'enum' && !in_array($v, $f[2], true)) $v = $f[1];
                if ($type === 'font' && !isset(Fonts::catalog()[$v])) $v = '';
                if ($type === 'weight') $v = isset(Fonts::WEIGHTS[(int) $v]) ? (string) (int) $v : '';
                if ($type === 'url' && $v !== '' && !preg_match('#^https?://#i', $v)) $v = 'https://' . ltrim($v, '/');
                if ($type === 'url' && $v !== '' && !filter_var($v, FILTER_VALIDATE_URL)) $v = '';
                if ($type === 'handle') $v = self::cleanHandle($k, $v);
                if ($type === 'text') $v = mb_substr($v, 0, $k === 'og_text' ? 200 : 300);
            }
            Setting::set("site_$k", $v);
        }
        self::$cache = null;
    }

    /** Aplica nombre y eslogan guardados al resto de la aplicación (que lee Config::get('app.name')). */
    public static function boot(): void
    {
        try {
            Config::set('app.name', self::get('name'));
            if (self::get('tagline') !== '') Config::set('app.tagline', self::get('tagline'));
        } catch (\Throwable) { /* BD aún no creada (instalación) */ }
    }

    // ---- Marca (archivos en uploads/brand) ----
    public static function brandFile(string $name): string { return rtrim((string) Config::get('uploads.dir'), '/') . '/brand/' . $name; }
    public static function hasBrand(string $name): bool { return is_file(self::brandFile($name)); }

    public static function brandUrl(string $name): ?string
    {
        if (!self::hasBrand($name)) return null;
        return Config::get('uploads.url') . '/brand/' . $name . '?v=' . (int) @filemtime(self::brandFile($name));
    }

    public static function absolute(string $path): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        $host  = preg_replace('/[^a-zA-Z0-9.:\\-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        return ($https ? 'https://' : 'http://') . $host . $path;
    }
}
