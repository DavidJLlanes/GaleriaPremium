<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Database;
use App\Models\Setting;

/**
 * Categorías de las fotos. Se guardan una vez y se reutilizan (desplegable); cada foto admite como máximo MAX.
 * Diseño del indicador = las mismas opciones que el botón de la portada (HeroButton::STYLES / SIZES), en Admin → Categorías.
 */
final class Categories
{
    public const MAX = 3;
    public const LEN = 30;

    /** Nombre limpio (sin etiquetas ni caracteres de control, espacios colapsados, máx. LEN). */
    public static function clean(string $n): string
    {
        $n = trim((string) preg_replace('/[\x00-\x1F\x7F<>"\\\\]+/u', '', $n));
        $n = trim((string) preg_replace('/\s+/u', ' ', $n));
        return mb_substr($n, 0, self::LEN);
    }

    /** Todas las categorías guardadas, con su número de fotos (orden alfabético). */
    public static function all(): array
    {
        return Database::all('SELECT c.id, c.name, COUNT(pc.photo_id) AS n FROM categories c LEFT JOIN photo_categories pc ON pc.category_id = c.id GROUP BY c.id ORDER BY c.name COLLATE NOCASE');
    }

    public static function names(): array { return array_column(self::all(), 'name'); }

    /** Interpreta lo que llega del formulario: JSON ["a","b"] o lista cats[]; devuelve nombres limpios, únicos y como mucho MAX. */
    public static function fromPost(mixed $raw): array
    {
        if (is_string($raw)) { $j = json_decode($raw, true); $raw = is_array($j) ? $j : ($raw === '' ? [] : [$raw]); }
        $out = [];
        foreach ((array) $raw as $n) {
            $n = self::clean((string) $n);
            if ($n !== '' && !in_array(mb_strtolower($n), array_map('mb_strtolower', $out), true)) $out[] = $n;
            if (count($out) >= self::MAX) break;
        }
        return $out;
    }

    /** Fija las categorías de una foto (crea las nuevas, reutiliza las existentes sin distinguir mayúsculas). */
    public static function set(int $photoId, array $names): void
    {
        $names = self::fromPost($names);
        $pdo = Database::pdo(); $pdo->beginTransaction();
        try {
            $pdo->prepare('DELETE FROM photo_categories WHERE photo_id = ?')->execute([$photoId]);
            $ins = $pdo->prepare('INSERT OR IGNORE INTO categories (name) VALUES (?)');
            $get = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
            $lnk = $pdo->prepare('INSERT OR IGNORE INTO photo_categories (photo_id, category_id, pos) VALUES (?, ?, ?)');
            foreach ($names as $pos => $n) { $ins->execute([$n]); $get->execute([$n]); $lnk->execute([$photoId, (int) $get->fetchColumn(), $pos]); }
            $pdo->commit();
        } catch (\Throwable $e) { $pdo->rollBack(); throw $e; }
    }

    /** Añade a cada foto de $rows la clave «cats» (lista de nombres, en su orden). */
    public static function attach(array $rows): array
    {
        $ids = array_map(fn ($r) => (int) $r['id'], $rows);
        $by = [];
        if ($ids) foreach (Database::all('SELECT pc.photo_id, c.name FROM photo_categories pc JOIN categories c ON c.id = pc.category_id WHERE pc.photo_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ') ORDER BY pc.pos', $ids) as $r) $by[(int) $r['photo_id']][] = $r['name'];
        foreach ($rows as &$r) $r['cats'] = $by[(int) $r['id']] ?? [];
        return $rows;
    }

    public static function rename(int $id, string $name): ?string
    {
        $name = self::clean($name);
        if ($name === '') return 'El nombre no puede estar vacío.';
        $dup = Database::one('SELECT id FROM categories WHERE name = ? AND id <> ?', [$name, $id]);
        if ($dup) return 'Ya existe una categoría con ese nombre.';
        Database::pdo()->prepare('UPDATE categories SET name = ? WHERE id = ?')->execute([$name, $id]);
        return null;
    }

    /** Borra la categoría (se quita de sus fotos; las fotos se conservan). */
    public static function delete(int $id): void { Database::pdo()->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]); }

    public static function find(int $id): ?array { return Database::one('SELECT c.id, c.name, (SELECT COUNT(*) FROM photo_categories WHERE category_id = c.id) AS n FROM categories c WHERE c.id = ?', [$id]); }

    /** Crea una categoría vacía. Devuelve [id, error]. */
    public static function create(string $name): array
    {
        $name = self::clean($name);
        if ($name === '') return [0, 'Escribe un nombre.'];
        if (Database::one('SELECT id FROM categories WHERE name = ?', [$name])) return [0, 'Ya existe una categoría con ese nombre.'];
        Database::pdo()->prepare('INSERT INTO categories (name) VALUES (?)')->execute([$name]);
        return [(int) Database::pdo()->lastInsertId(), null];
    }

    /** Ids de las fotos de una categoría (0 = fotos sin ninguna categoría). */
    public static function photoIds(int $cat): array
    {
        return array_map('intval', array_column($cat > 0
            ? Database::all('SELECT photo_id AS id FROM photo_categories WHERE category_id = ?', [$cat])
            : Database::all('SELECT id FROM photos WHERE id NOT IN (SELECT photo_id FROM photo_categories)'), 'id'));
    }

    /** Fotos de una categoría (0 = sin categoría) con el nombre de su galería y sus categorías. */
    public static function photos(int $cat, int $limit = 300): array
    {
        $ids = array_slice(self::photoIds($cat), 0, $limit);
        if (!$ids) return [];
        $rows = Database::all('SELECT p.*, g.title AS gallery_title FROM photos p JOIN galleries g ON g.id = p.gallery_id WHERE p.id IN (' . implode(',', array_fill(0, count($ids), '?')) . ') ORDER BY g.sort_order, g.id, p.sort_order, p.id', $ids);
        return self::attach($rows);
    }

    public static function untaggedCount(): int { return (int) Database::one('SELECT COUNT(*) AS n FROM photos WHERE id NOT IN (SELECT photo_id FROM photo_categories)')['n']; }

    /** Añade la categoría $cat a las fotos (respeta el máximo). Devuelve [añadidas, omitidas por tener ya el máximo]. */
    public static function addTo(array $photoIds, int $cat): array
    {
        $pdo = Database::pdo(); $add = 0; $skip = 0;
        $has = $pdo->prepare('SELECT COUNT(*) FROM photo_categories WHERE photo_id = ?'); $dup = $pdo->prepare('SELECT 1 FROM photo_categories WHERE photo_id = ? AND category_id = ?');
        $ins = $pdo->prepare('INSERT INTO photo_categories (photo_id, category_id, pos) VALUES (?, ?, ?)');
        foreach ($photoIds as $pid) {
            $dup->execute([$pid, $cat]); if ($dup->fetchColumn()) continue;
            $has->execute([$pid]); $n = (int) $has->fetchColumn();
            if ($n >= self::MAX) { $skip++; continue; }
            $ins->execute([$pid, $cat, $n]); $add++;
        }
        return [$add, $skip];
    }

    /** Quita la categoría de esas fotos (las fotos se conservan). */
    public static function removeFrom(array $photoIds, int $cat): int
    {
        if (!$photoIds) return 0;
        $st = Database::pdo()->prepare('DELETE FROM photo_categories WHERE category_id = ? AND photo_id IN (' . implode(',', array_fill(0, count($photoIds), '?')) . ')');
        $st->execute(array_merge([$cat], $photoIds));
        return $st->rowCount();
    }

    /** Pasa las fotos de $from a $to (en la misma posición; si ya tenían $to, solo se les quita $from). Devuelve cuántas fotos cambiaron. */
    public static function moveTo(array $photoIds, int $from, int $to): int
    {
        if ($from === $to || !$photoIds) return 0;
        $pdo = Database::pdo(); $pdo->beginTransaction(); $n = 0;
        try {
            $dup = $pdo->prepare('SELECT 1 FROM photo_categories WHERE photo_id = ? AND category_id = ?');
            $del = $pdo->prepare('DELETE FROM photo_categories WHERE photo_id = ? AND category_id = ?');
            $upd = $pdo->prepare('UPDATE photo_categories SET category_id = ? WHERE photo_id = ? AND category_id = ?');
            foreach ($photoIds as $pid) {
                $dup->execute([$pid, $to]);
                if ($dup->fetchColumn()) $del->execute([$pid, $from]); else $upd->execute([$to, $pid, $from]);
                $n++;
            }
            $pdo->commit();
        } catch (\Throwable $e) { $pdo->rollBack(); throw $e; }
        return $n;
    }

    /** Une varias categorías en $into: sus fotos pasan a $into y las demás categorías desaparecen. */
    public static function merge(array $ids, int $into): int
    {
        $n = 0;
        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if ($id === $into || !self::find($id)) continue;
            $n += self::moveTo(self::photoIds($id), $id, $into); self::delete($id);
        }
        return $n;
    }

    /** Borra las categorías que no tiene ninguna foto. Devuelve cuántas. */
    public static function deleteUnused(): int
    {
        $st = Database::pdo()->prepare('DELETE FROM categories WHERE id NOT IN (SELECT category_id FROM photo_categories)'); $st->execute();
        return $st->rowCount();
    }

    /** Diseño del indicador: style (los 7 del botón de la portada) y size (1–5). */
    public static function values(): array
    {
        $style = Setting::get('cat_style'); $size = (int) Setting::get('cat_size');
        return ['style' => isset(HeroButton::STYLES[$style]) ? $style : 'glass', 'size' => isset(HeroButton::SIZES[$size]) ? $size : 2];
    }

    public static function save(array $post): void
    {
        $s = (string) ($post['cat_style'] ?? ''); $z = (int) ($post['cat_size'] ?? 0);
        Setting::set('cat_style', isset(HeroButton::STYLES[$s]) ? $s : 'glass');
        Setting::set('cat_size', (string) (isset(HeroButton::SIZES[$z]) ? $z : 2));
    }
}
