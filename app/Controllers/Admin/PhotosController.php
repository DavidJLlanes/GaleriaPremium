<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Page;
use App\Models\{Gallery, Photo};
use App\Services\Auth;

/** Panel «Fotos»: todas las fotos de todas las galerías, con filtro, búsqueda y acciones masivas. */
final class PhotosController
{
    private const PER_PAGE = 48;

    public function index(array $p = []): void
    {
        Auth::guard();
        $gallery = (int) ($_GET['galeria'] ?? 0) ?: null;
        $q = trim((string) ($_GET['q'] ?? ''));
        $total = Photo::countSearch($gallery, $q);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = max(1, min($pages, (int) ($_GET['p'] ?? 1)));
        Page::render('pages/admin-photos', [
            'photos'    => Photo::search($gallery, $q, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'galleries' => Gallery::all(),
            'gallery'   => $gallery, 'q' => $q, 'total' => $total, 'page' => $page, 'pages' => $pages,
        ], ['title' => 'Fotos', 'nav' => 'fotos'], 'layouts/admin');
    }
}
