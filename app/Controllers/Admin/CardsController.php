<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Models\Gallery;
use App\Services\{Auth, Cards, Csrf, Flash};

/** Admin → Tarjetas: cómo se ven las galerías en la página principal (efecto de aparición, títulos y su diseño). */
final class CardsController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        Page::render('pages/admin-cards', [
            'c' => Cards::values(), 'reveals' => Cards::sorted(Module::all('reveals'), Cards::REVEAL_ORDER), 'titles' => Cards::sorted(Module::all('card-titles'), Cards::TITLE_ORDER), 'layouts' => Cards::sorted(Module::all('home-layouts'), Cards::LAYOUT_ORDER),
            'sample' => Gallery::published()[0] ?? null,
        ], ['title' => 'Tarjetas', 'nav' => 'tarjetas'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        Cards::save($_POST);
        Flash::set('Tarjetas guardadas.');
        Response::redirect('/admin/tarjetas');
    }
}
