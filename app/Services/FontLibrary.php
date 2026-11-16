<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Config;
use App\Models\Setting;

/**
 * Fuentes propias subidas desde Admin → Sitio → Tipografías (.woff2, .woff, .ttf, .otf).
 * Archivos en public/uploads/fonts/<id>/ · metadatos en el ajuste "custom_fonts" (JSON).
 * Se mezclan con el catálogo del tema en App\Services\Fonts::catalog().
 */
final class FontLibrary
{
    public const FORMATS = ['woff2' => 'woff2', 'woff' => 'woff', 'ttf' => 'truetype', 'otf' => 'opentype'];
    public const MAX_BYTES = 4 * 1024 * 1024;

    private static function dir(string $id = ''): string { return rtrim((string) Config::get('uploads.dir'), '/') . '/fonts' . ($id !== '' ? "/$id" : ''); }
    private static function url(string $id): string { return rtrim((string) Config::get('uploads.url'), '/') . "/fonts/$id"; }

    /** @return array<string, array{label:string, variants:list<array{file:string, weight:string, italic:bool, fmt:string}>}> */
    public static function raw(): array
    {
        $j = json_decode(Setting::get('custom_fonts', '{}'), true);
        return is_array($j) ? $j : [];
    }

    private static function save(array $all): void { Setting::set('custom_fonts', json_encode($all, JSON_UNESCAPED_UNICODE)); Fonts::reset(); }

    /** Entradas con el formato del catálogo (para Fonts::catalog()). */
    public static function catalog(): array
    {
        $out = [];
        foreach (self::raw() as $id => $f) {
            $faces = [];
            foreach ($f['variants'] ?? [] as $v) $faces[] = ['file' => $v['file'], 'weight' => $v['weight'], 'italic' => (bool) $v['italic'], 'base' => self::url($id), 'fmt' => $v['fmt']];
            if ($faces) $out[$id] = ['label' => $f['label'], 'cat' => 'custom', 'fallback' => 'system-ui,sans-serif', 'custom' => true, 'faces' => $faces];
        }
        return $out;
    }

    /** Tipo de fuente según la cabecera del archivo (no se fía de la extensión). */
    private static function detect(string $tmp): ?string
    {
        $h = (string) file_get_contents($tmp, false, null, 0, 4);
        return match (true) {
            $h === 'wOF2' => 'woff2', $h === 'wOFF' => 'woff', $h === 'OTTO' => 'otf',
            $h === "\x00\x01\x00\x00", $h === 'true' => 'ttf',
            default => null,
        };
    }

    /**
     * Añade una variante (peso + cursiva) a la familia $name; la crea si no existe.
     * $weight: '100'…'900' o 'var' (fuente variable 100–900). Devuelve el id de la familia.
     */
    public static function add(string $name, array $file, string $weight, bool $italic): string
    {
        $label = trim(preg_replace('/[\x00-\x1F\'"\\\\<>;{}]/u', '', $name) ?? '');
        $label = mb_substr($label, 0, 60);
        if ($label === '') throw new \RuntimeException('Escribe un nombre para la fuente.');
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new \RuntimeException('No se pudo subir el archivo (¿supera el límite del servidor?).');
        if ($file['size'] > self::MAX_BYTES) throw new \RuntimeException('El archivo supera 4 MB.');
        if (!is_uploaded_file($file['tmp_name']) && !is_file($file['tmp_name'])) throw new \RuntimeException('Archivo no válido.');
        $ext = self::detect($file['tmp_name']);
        if ($ext === null) throw new \RuntimeException('No parece una fuente válida (usa .woff2, .woff, .ttf u .otf).');
        $range = $weight === 'var' ? '100 900' : (string) (in_array((int) $weight, array_keys(Fonts::WEIGHTS), true) ? (int) $weight : 400);

        foreach (require ROOT . '/config/fonts.php' as $f)
            if (mb_strtolower($f['label']) === mb_strtolower($label)) throw new \RuntimeException("Ya existe una fuente del catálogo llamada «{$f['label']}». Usa otro nombre.");

        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower((string) (@iconv('UTF-8', 'ASCII//TRANSLIT', $label) ?: $label))), '-');
        $id = 'u-' . ($slug !== '' ? substr($slug, 0, 40) : 'fuente');

        $all = self::raw();
        if (!isset($all[$id])) $all[$id] = ['label' => $label, 'variants' => []];
        $name = $id . '-' . ($weight === 'var' ? 'var' : $range) . ($italic ? '-i' : '-n') . '.' . $ext;
        // misma variante ya subida → se reemplaza
        foreach ($all[$id]['variants'] as $k => $v)
            if ($v['weight'] === $range && (bool) $v['italic'] === $italic) { @unlink(self::dir($id) . '/' . $v['file']); unset($all[$id]['variants'][$k]); }
        if (count($all[$id]['variants']) >= 24) throw new \RuntimeException('Máximo 24 variantes por fuente.');

        if (!is_dir(self::dir($id)) && !@mkdir(self::dir($id), 0755, true) && !is_dir(self::dir($id))) throw new \RuntimeException('No se pudo crear la carpeta de fuentes.');
        $dst = self::dir($id) . '/' . $name;
        if (!@move_uploaded_file($file['tmp_name'], $dst) && !@copy($file['tmp_name'], $dst)) throw new \RuntimeException('No se pudo guardar el archivo.');
        @chmod($dst, 0644);
        $all[$id]['variants'][] = ['file' => $name, 'weight' => $range, 'italic' => $italic, 'fmt' => self::FORMATS[$ext]];
        $all[$id]['variants'] = array_values($all[$id]['variants']);
        self::save($all);
        return $id;
    }

    /** Borra la familia entera y la quita de los roles que la usaban. */
    public static function delete(string $id): void
    {
        $all = self::raw();
        if (!isset($all[$id])) return;
        $d = self::dir($id);
        if (is_dir($d)) { foreach (glob($d . '/*') ?: [] as $f) @unlink($f); @rmdir($d); }
        unset($all[$id]);
        self::save($all);
        foreach (array_keys(Fonts::ROLES) as $role) if (Site::get("font_$role") === $id) Setting::set("site_font_$role", '');
    }
}
