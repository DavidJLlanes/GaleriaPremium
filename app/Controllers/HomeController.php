<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\{Config, Module, Page};
use App\Models\{Gallery, Photo, Setting};

final class HomeController
{
    public function index(array $params = []): void
    {
        $galleries = Gallery::published();
        $hero = $this->hero($galleries);

        Page::render('pages/home', ['hero' => $hero, 'galleries' => $galleries], [
            'title'     => Config::get('app.name') . ' — ' . Config::get('app.tagline'),
            'bodyClass' => 'is-home',
            'isHome'    => true,
            'ogImage'   => $hero['photos'][0]['filename'] ?? null,
        ]);
    }

    /**
     * Resuelve el hero según los ajustes (Admin → Portada):
     *   hero_type   = single | gallery (cualquier carpeta modules/heroes/<tipo>/)
     *   hero_source = gallery (fotos de hero_gallery_id) | custom (hero_photo_ids, en el orden elegido)
     */
    private function hero(array $galleries): array
    {
        $type = Setting::get('hero_type', 'gallery');
        if (!Module::exists("heroes/$type")) $type = 'gallery';

        $custom = array_filter(explode(',', Setting::get('hero_photo_ids')), 'strlen');
        if (!$custom && Setting::get('hero_photo_id') !== '') $custom = [Setting::get('hero_photo_id')];   // compatibilidad
        $source = Setting::get('hero_source', $type === 'single' ? 'custom' : 'gallery');

        if ($source === 'custom' && $custom) {
            $photos = Photo::findMany($custom);
            if ($type === 'single') $photos = array_slice($photos, 0, 1);
        } else {
            $gid    = (int) Setting::get('hero_gallery_id', '0') ?: (int) ($galleries[0]['id'] ?? 0);
            $photos = $gid ? Photo::forGallery($gid) : [];
            if ($type === 'single') $photos = array_slice($photos, 0, 1);
        }
        if (!$photos) return ['type' => null, 'photos' => []];

        return [
            'type' => $type, 'photos' => $photos,
            'copy' => [
                'eyebrow' => Setting::get('hero_eyebrow', 'Portfolio fotográfico'),
                'title'   => Setting::get('hero_title') ?: Config::get('app.tagline'),
                'cta'     => Setting::get('hero_cta', 'Explorar galerías'),
            ],
            'interval' => max(2, min(30, (float) (Setting::get('hero_interval') ?: 6.5))) * 1000,
        ];
    }
}
