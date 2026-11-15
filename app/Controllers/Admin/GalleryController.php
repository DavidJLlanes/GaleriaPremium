<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Models\{Gallery, Photo};
use App\Services\{Auth, Csrf, Flash, PhotoService};
use App\Services\Ai\AiService;

final class GalleryController
{
    public function index(array $p = []): void
    {
        Auth::guard();
        Page::render('pages/admin-dashboard', [
            'galleries' => Gallery::all(),
            'styles'    => Module::all('galleries'),
        ], ['title' => 'Galerías', 'nav' => 'galerias'], 'layouts/admin');
    }

    public function create(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') { Flash::set('Escribe un nombre para la galería.', 'error'); Response::redirect('/admin'); }
        $id = Gallery::create($title, trim((string) ($_POST['description'] ?? '')), $this->style(), isset($_POST['published']));
        Flash::set('Galería creada. Ya puedes subir fotos.');
        Response::redirect("/admin/galeria/$id");
    }

    public function reorder(array $p): void
    {
        Auth::guard(true); Csrf::verify();
        $g = Gallery::find((int) $p['id']);
        if (!$g) Response::json(['ok' => false, 'error' => 'Galería no encontrada.'], 404);
        $ids = array_filter(explode(',', (string) ($_POST['ids'] ?? '')), 'strlen');
        Photo::reorder((int) $g['id'], $ids);
        Response::json(['ok' => true]);
    }

    public function show(array $p): void
    {
        Auth::guard();
        $g = Gallery::find((int) $p['id']);
        if (!$g) Response::redirect('/admin');
        Page::render('pages/admin-gallery', [
            'gallery'      => $g,
            'photos'       => Photo::forGallery((int) $g['id']),
            'styles'       => Module::all('galleries'),
            'aiConfigured' => AiService::isConfigured(),
            'aiAuto'       => AiService::settings()['auto'],
        ], ['title' => $g['title'], 'nav' => 'galerias'], 'layouts/admin');
    }

    public function update(array $p): void
    {
        Auth::guard(); Csrf::verify();
        $id = (int) $p['id'];
        $title = trim((string) ($_POST['title'] ?? ''));
        if (!Gallery::find($id) || $title === '') { Flash::set('Datos no válidos.', 'error'); Response::redirect("/admin/galeria/$id"); }
        Gallery::update($id, $title, trim((string) ($_POST['description'] ?? '')), $this->style(), isset($_POST['published']));
        Flash::set('Galería guardada.');
        Response::redirect("/admin/galeria/$id");
    }

    public function delete(array $p): void
    {
        Auth::guard(); Csrf::verify();
        Gallery::delete((int) $p['id']);
        Flash::set('Galería eliminada.');
        Response::redirect('/admin');
    }

    /** AJAX: una foto por petición (permite barra de progreso y subidas grandes). */
    public function upload(array $p): void
    {
        Auth::guard(true); Csrf::verify();
        $gid = (int) $p['id'];
        if (!Gallery::find($gid)) Response::json(['ok' => false, 'error' => 'La galería no existe'], 404);
        try {
            $photo = PhotoService::store(
                $_FILES['file'] ?? [],
                $gid,
                (string) ($_POST['title'] ?? ''),
                (string) ($_POST['description'] ?? ''),
                ($_POST['ai'] ?? '0') === '1',
            );
            Response::json(['ok' => true, 'photo' => $photo]);
        } catch (\RuntimeException $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    private function style(): string
    {
        $s = (string) ($_POST['style'] ?? 'masonry');
        return Module::exists("galleries/$s") ? $s : 'masonry';
    }
}
