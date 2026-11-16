<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\{Config, Database};

/**
 * Carga «desenfoque a nítido»: a cada <img> de una foto del sitio se le añade, en el HTML final, su miniatura minúscula
 * (photos.lqip, WebP 75% en data URI) como fondo. El CSS/JS de components/photo-load la muestra desenfocada hasta que llega la foto real.
 */
final class Lqip
{
    public static function inject(string $html): string
    {
        $base = preg_quote(rtrim((string) Config::get('uploads.url'), '/'), '~');
        if (!preg_match_all('~<img\b[^>]*\bsrc="' . $base . '/(?:thumbs|originals)/([^"/?]+)"~i', $html, $m)) return $html;
        $names = array_values(array_unique(array_map('rawurldecode', $m[1])));
        $map = [];
        foreach (array_chunk($names, 200) as $chunk) {
            $rows = Database::all('SELECT filename, lqip FROM photos WHERE lqip IS NOT NULL AND filename IN (' . implode(',', array_fill(0, count($chunk), '?')) . ')', $chunk);
            foreach ($rows as $r) $map[$r['filename']] = $r['lqip'];
        }
        if (!$map) return $html;
        return (string) preg_replace_callback('~<img\b[^>]*\bsrc="' . $base . '/(?:thumbs|originals)/([^"/?]+)"[^>]*>~i', static function (array $t) use ($map): string {
            $lq = $map[rawurldecode($t[1])] ?? null;
            if (!$lq || str_contains($t[0], 'data-lq')) return $t[0];
            $bg = 'background:#111 url(' . $lq . ') center/cover no-repeat';
            $tag = preg_match('~\sstyle="([^"]*)"~i', $t[0])
                ? preg_replace('~\sstyle="([^"]*)"~i', ' style="$1;' . $bg . '"', $t[0], 1)
                : preg_replace('~^<img\b~i', '<img style="' . $bg . '"', $t[0], 1);
            return preg_replace('~^<img\b~i', '<img data-lq', (string) $tag, 1);
        }, $html);
    }

    /** Rellena lqip de las fotos que aún no lo tienen (fotos anteriores a esta función). Devuelve cuántas. */
    public static function backfill(): int
    {
        $dir = rtrim((string) Config::get('uploads.dir'), '/'); $n = 0;
        foreach (Database::all("SELECT id, filename FROM photos WHERE lqip IS NULL OR lqip = ''") as $r) {
            $f = is_file("$dir/thumbs/{$r['filename']}") ? "$dir/thumbs/{$r['filename']}" : "$dir/originals/{$r['filename']}";
            if (!is_file($f)) continue;
            try { Database::pdo()->prepare('UPDATE photos SET lqip = ? WHERE id = ?')->execute([ImageProcessor::placeholder($f), $r['id']]); $n++; } catch (\Throwable) { /* se reintenta en el próximo despliegue */ }
        }
        return $n;
    }
}
