<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\{Config, Database};

final class Photo
{
    public static function forGallery(int $galleryId): array
    {
        return Database::all(
            'SELECT * FROM photos WHERE gallery_id = ? ORDER BY sort_order, id',
            [$galleryId]
        );
    }

    /** Guarda el orden de las fotos de una galería. $ids = ids en el orden deseado. */
    public static function reorder(int $galleryId, array $ids): void
    {
        $valid = array_column(Database::all('SELECT id FROM photos WHERE gallery_id = ?', [$galleryId]), 'id');
        $ids = array_values(array_filter(array_map('intval', $ids), fn($i) => in_array($i, array_map('intval', $valid), true)));
        $pdo = Database::pdo(); $pdo->beginTransaction();
        try {
            $st = $pdo->prepare('UPDATE photos SET sort_order = ? WHERE id = ? AND gallery_id = ?');
            foreach ($ids as $pos => $id) $st->execute([$pos, $id, $galleryId]);
            $pdo->commit();
        } catch (\Throwable $e) { $pdo->rollBack(); throw $e; }
    }

    public static function setCrop(int $id, ?array $c): void
    {
        Database::pdo()->prepare('UPDATE photos SET crop_x = ?, crop_y = ?, crop_w = ?, crop_h = ? WHERE id = ?')
            ->execute($c ? [$c['x'], $c['y'], $c['w'], $c['h'], $id] : [null, null, null, null, $id]);
    }

    /** Fotos por id respetando el orden dado (ids inexistentes se ignoran). */
    public static function findMany(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) return [];
        $rows = Database::all('SELECT * FROM photos WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')', $ids);
        $by = []; foreach ($rows as $r) $by[(int) $r['id']] = $r;
        return array_values(array_filter(array_map(static fn ($i) => $by[$i] ?? null, $ids)));
    }

    /** Todas las fotos con el nombre de su galería (selector de portada). */
    public static function allWithGallery(): array
    {
        return Database::all(
            'SELECT p.*, g.title AS gallery_title FROM photos p JOIN galleries g ON g.id = p.gallery_id
             ORDER BY g.sort_order, g.id, p.sort_order, p.id'
        );
    }

    /** Búsqueda en todas las galerías (panel «Fotos»): filtro por galería y texto en nombre/descripción. */
    public static function search(?int $gallery, string $q, int $limit, int $offset): array
    {
        [$w, $args] = self::filter($gallery, $q);
        return Database::all(
            "SELECT p.*, g.title AS gallery_title FROM photos p JOIN galleries g ON g.id = p.gallery_id $w
             ORDER BY g.sort_order, g.id, p.sort_order, p.id LIMIT $limit OFFSET $offset", $args);
    }

    public static function countSearch(?int $gallery, string $q): int
    {
        [$w, $args] = self::filter($gallery, $q);
        return (int) Database::one("SELECT COUNT(*) AS n FROM photos p $w", $args)['n'];
    }

    private static function filter(?int $gallery, string $q): array
    {
        $w = []; $args = [];
        if ($gallery) { $w[] = 'p.gallery_id = ?'; $args[] = $gallery; }
        if ($q !== '') { $w[] = "(p.title LIKE ? ESCAPE '\\' OR p.caption LIKE ? ESCAPE '\\')"; $args[] = $args[] = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%'; }
        return [$w ? 'WHERE ' . implode(' AND ', $w) : '', $args];
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM photos WHERE id = ?', [$id]);
    }

    public static function url(string $filename): string
    {
        return Config::get('uploads.url') . '/originals/' . rawurlencode($filename);
    }

    public static function thumbUrl(string $filename): string
    {
        return Config::get('uploads.url') . '/thumbs/' . rawurlencode($filename);
    }

    public static function create(int $galleryId, string $filename, string $title, string $caption, int $w, int $h, ?string $lqip = null, ?string $exif = null): int
    {
        $order = (int) Database::one('SELECT COALESCE(MAX(sort_order), -1) + 1 AS n FROM photos WHERE gallery_id = ?', [$galleryId])['n'];
        Database::pdo()->prepare(
            'INSERT INTO photos (gallery_id, filename, title, caption, width, height, sort_order, lqip, exif) VALUES (?,?,?,?,?,?,?,?,?)'
        )->execute([$galleryId, $filename, $title, $caption, $w, $h, $order, $lqip, $exif]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, string $title, string $caption): void
    {
        Database::pdo()->prepare('UPDATE photos SET title = ?, caption = ? WHERE id = ?')->execute([$title, $caption, $id]);
    }

    /**
     * Mueve fotos a otra galería (al final, conservando el orden dado). Devuelve los ids de las galerías de origen.
     * Los recortes se descartan: dependen de la proporción de cada galería y los recalcula Gallery::syncCrops().
     */
    public static function move(array $ids, int $toGallery): array
    {
        $photos = array_filter(self::findMany($ids), fn ($p) => (int) $p['gallery_id'] !== $toGallery);
        if (!$photos) return [];
        $pdo = Database::pdo(); $from = [];
        $order = (int) Database::one('SELECT COALESCE(MAX(sort_order), -1) + 1 AS n FROM photos WHERE gallery_id = ?', [$toGallery])['n'];
        $pdo->beginTransaction();
        try {
            $up = $pdo->prepare('UPDATE photos SET gallery_id = ?, sort_order = ?, crop_x = NULL, crop_y = NULL, crop_w = NULL, crop_h = NULL WHERE id = ?');
            $cv = $pdo->prepare('UPDATE galleries SET cover_photo_id = NULL WHERE id = ? AND cover_photo_id = ?');
            foreach ($photos as $p) {
                $from[(int) $p['gallery_id']] = true;
                $cv->execute([(int) $p['gallery_id'], (int) $p['id']]);
                $up->execute([$toGallery, $order++, (int) $p['id']]);
            }
            $pdo->commit();
        } catch (\Throwable $e) { $pdo->rollBack(); throw $e; }
        return array_keys($from);
    }

    /** Borra la fila y los archivos (original + miniatura). */
    public static function delete(int $id): void
    {
        $p = self::find($id);
        if (!$p) return;
        Database::pdo()->prepare('UPDATE galleries SET cover_photo_id = NULL WHERE cover_photo_id = ?')->execute([$id]);
        Database::pdo()->prepare('DELETE FROM photos WHERE id = ?')->execute([$id]);
        $dir = rtrim(Config::get('uploads.dir'), '/');
        foreach (['originals', 'thumbs'] as $d) @unlink("$dir/$d/" . $p['filename']);
    }
}
