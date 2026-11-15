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

        Page::render('pages/home', [
            'hero'      => $this->hero($galleries),
            'galleries' => $galleries,
        ], [
            'title'     => Config::get('app.name') . ' — ' . Config::get('app.tagline'),
            'bodyClass' => 'is-home',
        ]);
    }

    /**
     * Resuelve el hero según los ajustes:
     *   hero_type = single  + hero_photo_id   -> una foto a pantalla completa
     *   hero_type = gallery + hero_gallery_id -> pase de fotos de una galería
     * Para añadir otro tipo basta crear modules/heroes/<tipo>/.
     */
    private function hero(array $galleries): array
    {
        $type = Setting::get('hero_type', 'gallery');
        if (!Module::exists("heroes/$type")) $type = 'gallery';

        if ($type === 'single') {
            $photo  = Photo::find((int) Setting::get('hero_photo_id', '0'));
            $photos = $photo ? [$photo] : [];
        } else {
            $gid    = (int) Setting::get('hero_gallery_id', '0') ?: (int) ($galleries[0]['id'] ?? 0);
            $photos = $gid ? Photo::forGallery($gid) : [];
        }
        if (!$photos) return ['type' => null, 'photos' => []];

        return ['type' => $type, 'photos' => $photos];
    }
}
