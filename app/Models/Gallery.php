<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

final class Gallery
{
    /** Galerías publicadas con su foto de portada y nº de fotos. */
    public static function published(): array
    {
        return \App\Services\Pinned::apply(Database::all(
            'SELECT g.*, p.filename AS cover, p.width AS cover_w, p.height AS cover_h,
                    (SELECT COUNT(*) FROM photos WHERE gallery_id = g.id) AS photo_count
             FROM galleries g
             LEFT JOIN photos p ON p.id = COALESCE(g.cover_photo_id,
                    (SELECT id FROM photos WHERE gallery_id = g.id ORDER BY sort_order, id LIMIT 1))
             WHERE g.is_published = 1 AND g.kind = \'gallery\'
             ORDER BY g.sort_order, g.id'
        ), 'g');
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM galleries WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::one("SELECT * FROM galleries WHERE slug = ? AND is_published = 1 AND kind = 'gallery'", [$slug]);
    }

    /** Todas (incluidas ocultas), para el panel. */
    public static function all(): array
    {
        return \App\Services\Pinned::apply(Database::all(
            'SELECT g.*, (SELECT COUNT(*) FROM photos WHERE gallery_id = g.id) AS photo_count,
                    (SELECT filename FROM photos WHERE id = COALESCE(g.cover_photo_id,
                        (SELECT id FROM photos WHERE gallery_id = g.id ORDER BY sort_order, id LIMIT 1))) AS cover
             FROM galleries g WHERE g.kind = \'gallery\' ORDER BY g.sort_order, g.id'
        ), 'g');
    }

    /** Galerías Y sliders (panel Galerías: un único orden para todo). */
    public static function allMixed(): array
    {
        return \App\Services\Pinned::applyMixed(Database::all(
            'SELECT g.*, (SELECT COUNT(*) FROM photos WHERE gallery_id = g.id) AS photo_count,
                    (SELECT filename FROM photos WHERE id = COALESCE(g.cover_photo_id,
                        (SELECT id FROM photos WHERE gallery_id = g.id ORDER BY sort_order, id LIMIT 1))) AS cover
             FROM galleries g ORDER BY g.sort_order, g.id'
        ));
    }

    /** Lo que se pinta en la portada: galerías y sliders publicados en su orden (la fijada, primero). Los sliders sin fotos no salen. */
    public static function homeItems(): array
    {
        $rows = Database::all(
            'SELECT g.*, p.filename AS cover, p.width AS cover_w, p.height AS cover_h,
                    (SELECT COUNT(*) FROM photos WHERE gallery_id = g.id) AS photo_count
             FROM galleries g
             LEFT JOIN photos p ON p.id = COALESCE(g.cover_photo_id,
                    (SELECT id FROM photos WHERE gallery_id = g.id ORDER BY sort_order, id LIMIT 1))
             WHERE g.is_published = 1
             ORDER BY g.sort_order, g.id'
        );
        $rows = array_values(array_filter($rows, fn ($r) => $r['kind'] !== 'slider' || (int) $r['photo_count'] > 0));
        return \App\Services\Pinned::applyMixed($rows);
    }

    /** Alterna «destacada». */
    public static function toggleFeatured(int $id): bool
    {
        Database::pdo()->prepare('UPDATE galleries SET featured = 1 - featured WHERE id = ?')->execute([$id]);
        return (bool) (self::find($id)['featured'] ?? 0);
    }

    public static function setShowCats(int $id, bool $on): void
    {
        Database::pdo()->prepare('UPDATE galleries SET show_cats = ? WHERE id = ?')->execute([$on ? 1 : 0, $id]);
    }

    public static function setLightbox(int $id, string $fx): void
    {
        Database::pdo()->prepare('UPDATE galleries SET lightbox = ? WHERE id = ?')->execute([$fx, $id]);
    }

    /** Opciones propias del diseño elegido (JSON en galleries.options) con los valores por defecto del módulo. */
    public static function options(array $g): array
    {
        $saved = json_decode((string) ($g['options'] ?? '{}'), true) ?: [];
        $out = [];
        foreach ((\App\Core\Module::meta('galleries/' . $g['style'])['options'] ?? []) as $k => $o) {
            $v = $saved[$g['style']][$k] ?? $o['default'];
            $out[$k] = $v;
        }
        return $out;
    }

    public static function saveOptions(int $id, array $all): void
    {
        Database::pdo()->prepare('UPDATE galleries SET options = ? WHERE id = ?')->execute([json_encode($all, JSON_UNESCAPED_UNICODE), $id]);
    }

    /** ¿El diseño de esta galería usa recortes con proporción común? (module.php: 'crop' => true) */
    public static function usesCrop(array $g): bool
    {
        if (($g['kind'] ?? 'gallery') === 'slider') return true;   // los sliders siempre usan una proporción común
        return !empty(\App\Core\Module::meta('galleries/' . $g['style'])['crop']);
    }

    /** Foto que fija la proporción: en galerías, la primera por orden; en sliders, la PRIMERA SUBIDA (menor id), aunque se reordene. */
    public static function referencePhoto(int $id): ?array
    {
        $g = self::find($id); $photos = Photo::forGallery($id); if (!$photos) return null;
        if (($g['kind'] ?? 'gallery') !== 'slider') return $photos[0];
        usort($photos, fn ($a, $b) => (int) $a['id'] <=> (int) $b['id']);
        return $photos[0];
    }

    /**
     * Mantiene coherentes los recortes: la PRIMERA foto define la proporción y el resto se ajusta a ella
     * (las que no tengan recorte, o lo tengan con otra proporción, se centran automáticamente).
     */
    public static function syncCrops(int $id): void
    {
        $g = self::find($id);
        if (!$g || !self::usesCrop($g)) return;
        $photos = Photo::forGallery($id);
        if (!$photos) { Database::pdo()->prepare('UPDATE galleries SET crop_ratio = NULL WHERE id = ?')->execute([$id]); return; }

        $first = self::referencePhoto($id); $fw = max(1, (int) $first['width']); $fh = max(1, (int) $first['height']);
        $fc = \App\Services\Crop::of($first);
        if ($fc) { $ratio = \App\Services\Crop::ratio($fc, $fw, $fh); }
        else {
            // slider ya con proporción: se conserva aunque cambie la foto de referencia; si no, la natural de la primera
            $ratio = ($g['kind'] ?? 'gallery') === 'slider' && (float) ($g['crop_ratio'] ?? 0) > 0 ? (float) $g['crop_ratio'] : max(.5, min(2.0, $fw / $fh));
            Photo::setCrop((int) $first['id'], \App\Services\Crop::fit($ratio, $fw, $fh));
        }
        foreach ($photos as $p) {
            if ((int) $p['id'] === (int) $first['id']) continue;
            $iw = max(1, (int) $p['width']); $ih = max(1, (int) $p['height']);
            $c = \App\Services\Crop::of($p);
            if (!$c || abs(\App\Services\Crop::ratio($c, $iw, $ih) / $ratio - 1) > .01) Photo::setCrop((int) $p['id'], \App\Services\Crop::fit($ratio, $iw, $ih, $c));
        }
        Database::pdo()->prepare('UPDATE galleries SET crop_ratio = ? WHERE id = ?')->execute([$ratio, $id]);
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

    /** Crea un slider (fila con kind = slider) con la configuración por defecto. */
    public static function createSlider(string $title): int
    {
        $order = (int) Database::one('SELECT COALESCE(MAX(sort_order), -1) + 1 AS n FROM galleries')['n'];
        Database::pdo()->prepare("INSERT INTO galleries (slug, title, description, style, is_published, sort_order, kind, options) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([self::uniqueSlug('slider-' . self::slugify($title)), $title, '', 'slider', 1, $order, 'slider', json_encode(['slider' => \App\Services\Slider::defaults()])]);
        return (int) Database::pdo()->lastInsertId();
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
        \App\Services\Pinned::clear('g', $id); \App\Services\Pinned::clear('s', $id);
    }
}
