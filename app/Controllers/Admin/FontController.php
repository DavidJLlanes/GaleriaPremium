<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Response;
use App\Services\{Auth, Csrf, FontLibrary, Fonts};

/** Admin → Sitio → Tipografías: subir y borrar fuentes propias (JSON). */
final class FontController
{
    public function upload(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        try {
            $id = FontLibrary::add((string) ($_POST['name'] ?? ''), $_FILES['file'] ?? [], (string) ($_POST['weight'] ?? '400'), !empty($_POST['italic']));
        } catch (\RuntimeException $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
        Response::json(['ok' => true, 'id' => $id, 'label' => Fonts::catalog()[$id]['label'], 'meta' => Fonts::meta($id), 'variants' => self::variants($id)]);
    }

    public function delete(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        $id = (string) ($_POST['id'] ?? '');
        if (!str_starts_with($id, 'u-')) Response::json(['ok' => false, 'error' => 'Solo se pueden borrar fuentes propias'], 422);
        FontLibrary::delete($id);
        Response::json(['ok' => true]);
    }

    /** Etiquetas legibles de las variantes (para la lista del panel). */
    public static function variants(string $id): array
    {
        return array_map(fn ($f) => ($f['weight'] === '100 900' ? 'Variable' : (Fonts::WEIGHTS[(int) $f['weight']] ?? $f['weight'])) . ($f['italic'] ? ' cursiva' : ''), Fonts::faces($id));
    }
}
