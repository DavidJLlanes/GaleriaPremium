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

    /** Reordena las galerías (arrastrando en el panel). */
    public function reorderAll(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        Gallery::reorder(array_filter(explode(',', (string) ($_POST['ids'] ?? '')), 'strlen'));
        Response::json(['ok' => true]);
    }

    /** Fijar como primera / destacar (alterna). POST flag = pin | featured. */
    public function flag(array $p): void
    {
        Auth::guard(true); Csrf::verify();
        $g = Gallery::find((int) $p['id']);
        if (!$g) Response::json(['ok' => false, 'error' => 'Galería no encontrada.'], 404);
        $on = ($_POST['flag'] ?? '') === 'pin' ? \App\Services\Pinned::toggle('g', (int) $g['id']) : Gallery::toggleFeatured((int) $g['id']);
        Response::json(['ok' => true, 'on' => $on]);
    }

    public function reorder(array $p): void
    {
        Auth::guard(true); Csrf::verify();
        $g = Gallery::find((int) $p['id']);
        if (!$g) Response::json(['ok' => false, 'error' => 'Galería no encontrada.'], 404);
        $ids = array_filter(explode(',', (string) ($_POST['ids'] ?? '')), 'strlen');
        Photo::reorder((int) $g['id'], $ids);
        Gallery::syncCrops((int) $g['id']);   // la primera foto define la proporción
        Response::json(['ok' => true, 'ratio' => (float) (Gallery::find((int) $g['id'])['crop_ratio'] ?? 0)]);
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
            'canCrop'      => Gallery::usesCrop($g),
            'lightboxes'   => Module::all('lightboxes'),
            'options'      => Gallery::options($g),
        ], ['title' => $g['title'], 'nav' => 'galerias'], 'layouts/admin');
    }

    public function update(array $p): void
    {
        Auth::guard(); Csrf::verify();
        $id = (int) $p['id'];
        $title = trim((string) ($_POST['title'] ?? ''));
        if (!Gallery::find($id) || $title === '') { Flash::set('Datos no válidos.', 'error'); Response::redirect("/admin/galeria/$id"); }
        Gallery::update($id, $title, trim((string) ($_POST['description'] ?? '')), $this->style(), isset($_POST['published']));
        Gallery::saveOptions($id, $this->options());
        $fx = (string) ($_POST['lightbox'] ?? 'fade');
        Gallery::setLightbox($id, Module::exists("lightboxes/$fx") ? $fx : 'fade');
        Gallery::syncCrops($id);
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

    /** Borrado masivo de galerías (con todas sus fotos). */
    public function bulkDelete(): void
    {
        Auth::guard(true); Csrf::verify();
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) ($_POST['ids'] ?? [])))));
        $n = 0;
        foreach ($ids as $id) if (Gallery::find($id)) { Gallery::delete($id); $n++; }
        if (!$n) Response::json(['ok' => false, 'error' => 'No hay galerías seleccionadas'], 422);
        Response::json(['ok' => true, 'count' => $n]);
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
            Gallery::syncCrops($gid);
            Response::json(['ok' => true, 'photo' => $photo]);
        } catch (\RuntimeException $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    /** Opciones de cada diseño enviadas como opt[<diseño>][<clave>]; se validan contra el module.php de cada uno. */
    private function options(): array
    {
        $out = [];
        foreach (Module::all('galleries') as $style => $meta) {
            foreach ($meta['options'] ?? [] as $key => $o) {
                $raw = $_POST['opt'][$style][$key] ?? null;
                $out[$style][$key] = match ($o['type']) {
                    'bool'   => $raw ? 1 : 0,
                    'number' => max($o['min'] ?? 0, min($o['max'] ?? 999, (int) $raw)),
                    'enum'   => array_key_exists((string) $raw, $o['choices']) ? (string) $raw : $o['default'],
                    default  => $o['default'],
                };
            }
        }
        return $out;
    }

    private function style(): string
    {
        $s = (string) ($_POST['style'] ?? 'masonry');
        return Module::exists("galleries/$s") ? $s : 'masonry';
    }
}
