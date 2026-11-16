<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\{Config, Module, Page};
use App\Models\{Gallery, Photo, Setting};
use App\Services\HeroOptions;

final class HomeController
{
    public function index(array $params = []): void
    {
        $galleries = Gallery::published();
        $hero = $this->hero($galleries);

        Page::render('pages/home', ['hero' => $hero, 'galleries' => $galleries, 'items' => Gallery::homeItems()], [
            'title'     => Config::get('app.name') . ' — ' . Config::get('app.tagline'),
            'bodyClass' => 'is-home',
            'isHome'    => true,
            'ogImage'   => $hero['photos'][0]['filename'] ?? null,
        ]);
    }

    /**
     * Resuelve el hero según los ajustes (Admin → Portada):
     *   hero_type   = cualquier carpeta modules/heroes/<tipo>/ (su module.php dice si usa 0, 1 o varias fotos)
     *   hero_source = gallery (fotos de hero_gallery_id) | custom (hero_photo_ids, en el orden elegido)
     */
    private function hero(array $galleries): array
    {
        $type = Setting::get('hero_type', 'gallery');
        if (!Module::exists("heroes/$type")) $type = 'gallery';
        $mode = Module::meta("heroes/$type")['photos'] ?? 'many';       // none | one | many (module.php del hero)

        $custom = array_filter(explode(',', Setting::get('hero_photo_ids')), 'strlen');
        if (!$custom && Setting::get('hero_photo_id') !== '') $custom = [Setting::get('hero_photo_id')];   // compatibilidad
        $source = Setting::get('hero_source', $mode === 'one' ? 'custom' : 'gallery');

        if ($mode === 'none') $photos = [];
        elseif ($source === 'custom' && $custom) $photos = Photo::findMany($custom);
        else {
            $gid    = (int) Setting::get('hero_gallery_id', '0') ?: (int) ($galleries[0]['id'] ?? 0);
            $photos = $gid ? Photo::forGallery($gid) : [];
        }
        if ($mode === 'one') $photos = array_slice($photos, 0, 1);
        if (!$photos && $mode !== 'none') return ['type' => null, 'photos' => []];

        return [
            'type' => $type, 'photos' => $photos,
            'copy' => [
                'eyebrow' => (string) Config::get('app.tagline', ''),   // eslogan: Sitio → Identidad
                'title'   => (string) Config::get('app.name'),           // nombre de la web: Sitio → Identidad
                'cta'     => Setting::get('hero_cta', 'Explorar galerías'),
            ],
            'interval' => max(2, min(30, (float) (Setting::get('hero_interval') ?: 6.5))) * 1000,
            'opts'     => HeroOptions::values($type),
            'stats'    => ['galleries' => count($galleries), 'photos' => array_sum(array_column($galleries, 'photo_count'))],
        ];
    }
}
