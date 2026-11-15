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

    public static function create(int $galleryId, string $filename, string $title, string $caption, int $w, int $h): int
    {
        $order = (int) Database::one('SELECT COALESCE(MAX(sort_order), -1) + 1 AS n FROM photos WHERE gallery_id = ?', [$galleryId])['n'];
        Database::pdo()->prepare(
            'INSERT INTO photos (gallery_id, filename, title, caption, width, height, sort_order) VALUES (?,?,?,?,?,?,?)'
        )->execute([$galleryId, $filename, $title, $caption, $w, $h, $order]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, string $title, string $caption): void
    {
        Database::pdo()->prepare('UPDATE photos SET title = ?, caption = ? WHERE id = ?')->execute([$title, $caption, $id]);
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
