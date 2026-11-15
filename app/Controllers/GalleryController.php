<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\{Config, Module, Page};
use App\Models\{Gallery, Photo};

final class GalleryController
{
    public function show(array $params): void
    {
        $gallery = Gallery::findBySlug($params['slug'] ?? '');
        if (!$gallery) { Page::notFound(); return; }

        // El estilo es un módulo: modules/galleries/<estilo>/
        $style = Module::exists('galleries/' . $gallery['style']) ? $gallery['style'] : 'masonry';

        Page::render('pages/gallery', [
            'gallery' => $gallery,
            'photos'  => Photo::forGallery((int) $gallery['id']),
            'style'   => $style,
        ], [
            'title' => $gallery['title'] . ' — ' . Config::get('app.name'),
        ]);
    }
}
