<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Setting;

/** Elemento «fijado» de la portada (siempre el primero). Solo UNO en total entre galerías y sliders: ajuste `pinned_item` = "g:<id>" | "s:<id>". */
final class Pinned
{
    public static function get(): string { return Setting::get('pinned_item', ''); }
    public static function is(string $type, int $id): bool { return self::get() === $type . ':' . $id; }
    /** Fija (y quita la fijación a cualquier otro) o, si ya estaba fijado, lo suelta. Devuelve si queda fijado. */
    public static function toggle(string $type, int $id): bool
    {
        if (self::is($type, $id)) { Setting::set('pinned_item', ''); return false; }
        Setting::set('pinned_item', $type . ':' . $id); return true;
    }
    public static function clear(string $type, int $id): void { if (self::is($type, $id)) Setting::set('pinned_item', ''); }
    /** Marca `pinned` en cada fila y la mueve al principio (el resto conserva su orden). */
    public static function apply(array $rows, string $type): array
    {
        $first = []; $rest = [];
        foreach ($rows as $r) { $r['pinned'] = self::is($type, (int) $r['id']) ? 1 : 0; $r['pinned'] ? $first[] = $r : $rest[] = $r; }
        return array_merge($first, $rest);
    }
}
