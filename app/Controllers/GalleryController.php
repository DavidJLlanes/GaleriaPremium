<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\{Config, View};
use App\Models\{Gallery, Photo};

final class GalleryController
{
    public function show(array $params): void
    {
        $gallery = Gallery::findBySlug($params['slug'] ?? '');
        if (!$gallery) {
            http_response_code(404);
            View::render('404', ['title' => 'No encontrada']);
            return;
        }
        $styles = Config::get('gallery_styles', []);
        $style  = isset($styles[$gallery['style']]) ? $gallery['style'] : 'masonry';

        View::render('gallery', [
            'title'   => $gallery['title'] . ' — ' . Config::get('app.name'),
            'gallery' => $gallery,
            'photos'  => Photo::forGallery((int) $gallery['id']),
            'style'   => $style,
        ]);
    }
}
