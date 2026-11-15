<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Models\{Gallery, Photo, Setting};
use App\Services\{Auth, Csrf, Flash};

/** Admin → Portada: tipo de hero, fotos (y su orden), textos y velocidad. */
final class HeroController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        $ids = array_filter(explode(',', Setting::get('hero_photo_ids') ?: Setting::get('hero_photo_id')), 'strlen');
        Page::render('pages/admin-hero', [
            'heroes'    => Module::all('heroes'),
            'type'      => Setting::get('hero_type', 'gallery'),
            'source'    => Setting::get('hero_source', Setting::get('hero_type', 'gallery') === 'single' ? 'custom' : 'gallery'),
            'galleryId' => (int) Setting::get('hero_gallery_id', '0'),
            'galleries' => Gallery::all(),
            'selected'  => Photo::findMany($ids),
            'photos'    => Photo::allWithGallery(),
            'eyebrow'   => Setting::get('hero_eyebrow', 'Portfolio fotográfico'),
            'title'     => Setting::get('hero_title'),
            'cta'       => Setting::get('hero_cta', 'Explorar galerías'),
            'interval'  => Setting::get('hero_interval') ?: '6.5',
        ], ['title' => 'Portada', 'nav' => 'portada'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        $type = (string) ($_POST['hero_type'] ?? 'gallery');
        if (!Module::exists("heroes/$type")) $type = 'gallery';
        $source = ($_POST['hero_source'] ?? '') === 'custom' ? 'custom' : 'gallery';
        $ids = array_map(fn ($r) => (string) $r['id'], Photo::findMany(array_filter(explode(',', (string) ($_POST['hero_photo_ids'] ?? '')), 'strlen')));
        if ($type === 'single') $ids = array_slice($ids, 0, 1);

        Setting::set('hero_type', $type);
        Setting::set('hero_source', $source);
        Setting::set('hero_gallery_id', (string) (int) ($_POST['hero_gallery_id'] ?? 0));
        Setting::set('hero_photo_ids', implode(',', $ids));
        Setting::set('hero_photo_id', '');
        Setting::set('hero_eyebrow', mb_substr(trim((string) ($_POST['hero_eyebrow'] ?? '')), 0, 80));
        Setting::set('hero_title', mb_substr(trim((string) ($_POST['hero_title'] ?? '')), 0, 160));
        Setting::set('hero_cta', mb_substr(trim((string) ($_POST['hero_cta'] ?? '')), 0, 40));
        Setting::set('hero_interval', (string) max(2, min(30, (float) str_replace(',', '.', (string) ($_POST['hero_interval'] ?? '6.5')))));

        Flash::set('Portada guardada.');
        Response::redirect('/admin/portada');
    }
}
