<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

final class Gallery
{
    /** Galerías publicadas con su foto de portada y nº de fotos. */
    public static function published(): array
    {
        return Database::all(
            'SELECT g.*, p.filename AS cover, p.width AS cover_w, p.height AS cover_h,
                    (SELECT COUNT(*) FROM photos WHERE gallery_id = g.id) AS photo_count
             FROM galleries g
             LEFT JOIN photos p ON p.id = COALESCE(g.cover_photo_id,
                    (SELECT id FROM photos WHERE gallery_id = g.id ORDER BY sort_order, id LIMIT 1))
             WHERE g.is_published = 1
             ORDER BY g.sort_order, g.id'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM galleries WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::one('SELECT * FROM galleries WHERE slug = ? AND is_published = 1', [$slug]);
    }

    /** Todas (incluidas ocultas), para el panel. */
    public static function all(): array
    {
        return Database::all(
            'SELECT g.*, (SELECT COUNT(*) FROM photos WHERE gallery_id = g.id) AS photo_count,
                    (SELECT filename FROM photos WHERE id = COALESCE(g.cover_photo_id,
                        (SELECT id FROM photos WHERE gallery_id = g.id ORDER BY sort_order, id LIMIT 1))) AS cover
             FROM galleries g ORDER BY g.sort_order, g.id'
        );
    }

    /** Guarda el orden de las galerías (ids en el orden deseado). */
    public static function reorder(array $ids): void
    {
        $pdo = Database::pdo(); $pdo->beginTransaction();
        try {
            $st = $pdo->prepare('UPDATE galleries SET sort_order = ? WHERE id = ?');
            foreach (array_values(array_unique(array_map('intval', $ids))) as $pos => $id) $st->execute([$pos, $id]);
            $pdo->commit();
        } catch (\Throwable $e) { $pdo->rollBack(); throw $e; }
    }

    public static function slugify(string $text): string
    {
        $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $t = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $t), '-'));
        return $t !== '' ? $t : 'galeria';
    }

    public static function uniqueSlug(string $base, ?int $exceptId = null): string
    {
        $slug = $base; $i = 2;
        while (Database::one('SELECT id FROM galleries WHERE slug = ? AND id != ?', [$slug, $exceptId ?? 0])) $slug = $base . '-' . $i++;
        return $slug;
    }

    public static function create(string $title, string $description, string $style, bool $published): int
    {
        $order = (int) Database::one('SELECT COALESCE(MAX(sort_order), -1) + 1 AS n FROM galleries')['n'];
        Database::pdo()->prepare('INSERT INTO galleries (slug, title, description, style, is_published, sort_order) VALUES (?,?,?,?,?,?)')
            ->execute([self::uniqueSlug(self::slugify($title)), $title, $description, $style, (int) $published, $order]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, string $title, string $description, string $style, bool $published): void
    {
        Database::pdo()->prepare('UPDATE galleries SET title = ?, description = ?, style = ?, is_published = ? WHERE id = ?')
            ->execute([$title, $description, $style, (int) $published, $id]);
    }

    public static function setCover(int $galleryId, ?int $photoId): void
    {
        Database::pdo()->prepare('UPDATE galleries SET cover_photo_id = ? WHERE id = ?')->execute([$photoId, $galleryId]);
    }

    public static function delete(int $id): void
    {
        foreach (Photo::forGallery($id) as $p) Photo::delete((int) $p['id']);
        Database::pdo()->prepare('DELETE FROM galleries WHERE id = ?')->execute([$id]);
    }
}
