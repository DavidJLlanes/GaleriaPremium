<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Database, Page, Response};
use App\Models\{Gallery, Photo};
use App\Services\{Auth, Categories, Csrf, Flash, HeroButton};

/** Admin → Categorías: diseño del indicador (mismas opciones que el botón de la portada) y gestión de las categorías guardadas. */
final class CategoriesController
{
    public function index(array $p = []): void
    {
        Auth::guard();
        Page::render('pages/admin-categories', ['c' => Categories::values(), 'styles' => HeroButton::STYLES, 'sizes' => HeroButton::SIZES, 'cats' => Categories::all()], ['title' => 'Categorías', 'nav' => 'categorias'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        Categories::save($_POST);
        Flash::set('Diseño de las categorías guardado.');
        Response::redirect('/admin/categorias');
    }

    public function create(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        [$id, $err] = Categories::create((string) ($_POST['name'] ?? ''));
        if ($err) Response::json(['ok' => false, 'error' => $err], 422);
        Response::json(['ok' => true, 'id' => $id]);
    }

    /** Ficha de una categoría con sus fotos (id 0 = fotos sin categoría). */
    public function show(array $p): void
    {
        Auth::guard();
        $id = (int) $p['id'];
        $cat = $id > 0 ? Categories::find($id) : ['id' => 0, 'name' => 'Sin categoría', 'n' => Categories::untaggedCount()];
        if (!$cat) Response::redirect('/admin/categorias');
        Page::render('pages/admin-category', [
            'cat' => $cat, 'photos' => Categories::photos($id), 'all' => Categories::all(),
            'galleries' => array_values(array_filter(Gallery::allMixed(), fn ($g) => true)),
        ], ['title' => $cat['name'], 'nav' => 'categorias'], 'layouts/admin');
    }

    public function rename(array $p): void
    {
        Auth::guard(true); Csrf::verify();
        $id = (int) $p['id']; $name = Categories::clean((string) ($_POST['name'] ?? ''));
        $dup = $name !== '' ? Database::one('SELECT id FROM categories WHERE name = ? AND id <> ?', [$name, $id]) : null;
        if ($dup) Response::json(['ok' => false, 'error' => "Ya existe «{$name}». ¿Quieres unirlas?", 'dup' => (int) $dup['id']], 422);
        $err = Categories::rename($id, $name);
        if ($err) Response::json(['ok' => false, 'error' => $err], 422);
        Response::json(['ok' => true]);
    }

    /** Borra la categoría. Con with_photos=1 borra también sus fotos; si no, las fotos se conservan. */
    public function delete(array $p): void
    {
        Auth::guard(true); Csrf::verify();
        $id = (int) $p['id']; $photos = 0;
        if (!Categories::find($id)) Response::json(['ok' => false, 'error' => 'La categoría no existe'], 404);
        if (!empty($_POST['with_photos'])) {
            $galleries = [];
            foreach (Photo::findMany(Categories::photoIds($id)) as $ph) { $galleries[(int) $ph['gallery_id']] = true; Photo::delete((int) $ph['id']); $photos++; }
            foreach (array_keys($galleries) as $g) Gallery::syncCrops($g);
        }
        Categories::delete($id);
        Response::json(['ok' => true, 'photos' => $photos]);
    }

    /** Pasa TODAS las fotos de una categoría a otra (la de origen se conserva, vacía). */
    public function moveAll(array $p): void
    {
        Auth::guard(true); Csrf::verify();
        $from = (int) $p['id']; $to = (int) ($_POST['to'] ?? 0);
        if (!Categories::find($from) || !Categories::find($to) || $from === $to) Response::json(['ok' => false, 'error' => 'Elige otra categoría de destino'], 422);
        Response::json(['ok' => true, 'moved' => Categories::moveTo(Categories::photoIds($from), $from, $to)]);
    }

    /** Une varias categorías en una (ids[] → into). */
    public function merge(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        $into = (int) ($_POST['into'] ?? 0); $ids = array_filter(array_map('intval', (array) ($_POST['ids'] ?? [])), fn ($i) => $i !== $into);
        if (!Categories::find($into) || count($ids) < 1) Response::json(['ok' => false, 'error' => 'Elige al menos dos categorías y la de destino'], 422);
        Response::json(['ok' => true, 'moved' => Categories::merge($ids, $into)]);
    }

    public function clean(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        Response::json(['ok' => true, 'count' => Categories::deleteUnused()]);
    }

    /** Acciones sobre fotos seleccionadas: add (añadir categoría), move (de from a to), remove (quitar from). */
    public function photos(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) ($_POST['ids'] ?? [])))));
        $act = (string) ($_POST['action'] ?? ''); $from = (int) ($_POST['from'] ?? 0); $to = (int) ($_POST['to'] ?? 0);
        if (!$ids) Response::json(['ok' => false, 'error' => 'No hay fotos seleccionadas'], 422);
        $r = ['ok' => true, 'changed' => 0, 'skipped' => 0];
        switch ($act) {
            case 'add':
                if (!Categories::find($to)) Response::json(['ok' => false, 'error' => 'Elige una categoría'], 422);
                [$r['changed'], $r['skipped']] = Categories::addTo($ids, $to); break;
            case 'move':
                if (!Categories::find($from) || !Categories::find($to)) Response::json(['ok' => false, 'error' => 'Elige una categoría de destino'], 422);
                $r['changed'] = Categories::moveTo($ids, $from, $to); break;
            case 'remove':
                if (!Categories::find($from)) Response::json(['ok' => false, 'error' => 'La categoría no existe'], 422);
                $r['changed'] = Categories::removeFrom($ids, $from); break;
            default: Response::json(['ok' => false, 'error' => 'Acción no válida'], 422);
        }
        Response::json($r);
    }
}
