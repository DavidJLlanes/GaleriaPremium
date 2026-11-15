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

    public static function findBySlug(string $slug): ?array
    {
        return Database::one('SELECT * FROM galleries WHERE slug = ? AND is_published = 1', [$slug]);
    }
}
