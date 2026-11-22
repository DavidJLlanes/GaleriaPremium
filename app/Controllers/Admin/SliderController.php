<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Models\{Gallery, Photo};
use App\Services\{Auth, Csrf, Flash, Slider};
use App\Services\Ai\AiService;

/** Admin → Sliders: crear y configurar sliders (las fotos, el orden y el borrado masivo reutilizan las rutas de galería). */
final class SliderController
{
    public function index(array $p = []): void
    {
        Auth::guard();
        Page::render('pages/admin-sliders', ['sliders' => Slider::all(), 'designs' => Module::all('sliders')], ['title' => 'Sliders', 'nav' => 'sliders'], 'layouts/admin');
    }

    public function create(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') { Flash::set('Escribe un nombre para el slider.', 'error'); Response::redirect('/admin/sliders'); }
        $id = Gallery::createSlider($title);
        Flash::set('Slider creado. Sube su primera foto: fija la proporción de todas.');
        Response::redirect("/admin/slider/$id");
    }

    public function show(array $p): void
    {
        Auth::guard();
        $g = Gallery::find((int) $p['id']);
        if (!$g || $g['kind'] !== 'slider') Response::redirect('/admin/sliders');
        $pick = [];
        foreach (Slider::SPEC as $k => $s) if ($s['type'] === 'module') {
            $pick[$k] = (isset($s['none']) ? ['none' => ['label' => $s['none'], 'desc' => 'No se muestra.', 'icon' => '<path d="M20 20h24" stroke="currentColor" stroke-opacity=".4"/>']] : []) + Module::all($s['kind']);
        }
        Page::render('pages/admin-slider', [
            'gallery' => $g, 'photos' => Photo::forGallery((int) $g['id'], true), 'cfg' => Slider::config($g), 'pick' => $pick,
            'lightboxes' => Module::all('lightboxes'), 'aiConfigured' => AiService::isConfigured(), 'aiAuto' => AiService::settings()['auto'],
        ], ['title' => $g['title'], 'nav' => 'sliders'], 'layouts/admin');
    }

    public function update(array $p): void
    {
        Auth::guard(); Csrf::verify();
        $id = (int) $p['id']; $g = Gallery::find($id);
        $title = trim((string) ($_POST['title'] ?? ''));
        if (!$g || $g['kind'] !== 'slider' || $title === '') { Flash::set('Datos no válidos.', 'error'); Response::redirect('/admin/sliders'); }
        Gallery::update($id, $title, '', 'slider', isset($_POST['published']));
        Slider::save($id, $_POST);
        $fx = (string) ($_POST['lightbox'] ?? 'fade');
        Gallery::setLightbox($id, Module::exists("lightboxes/$fx") ? $fx : 'fade');
        Gallery::setShowCats($id, isset($_POST['show_cats']));
        Gallery::setShowExif($id, isset($_POST['show_exif']));
        Flash::set('Slider guardado.');
        Response::redirect("/admin/slider/$id");
    }

    public function delete(array $p): void
    {
        Auth::guard(); Csrf::verify();
        $g = Gallery::find((int) $p['id']);
        if ($g && $g['kind'] === 'slider') Gallery::delete((int) $g['id']);
        Flash::set('Slider eliminado.');
        Response::redirect('/admin/sliders');
    }
}
