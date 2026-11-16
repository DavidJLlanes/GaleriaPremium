<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Services\{Auth, Csrf, Flash, Footer};

/** Admin → Pie: diseño del pie, efecto de llegada, contenido y firma. */
final class FooterController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        Page::render('pages/admin-footer', ['v' => Footer::values(), 'designs' => Module::all('footers'), 'reveals' => Module::all('footer-reveals')], ['title' => 'Pie', 'nav' => 'pie'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        Footer::save((array) ($_POST['ft'] ?? []), (array) ($_POST['fo'] ?? []), (array) ($_POST['fr'] ?? []));
        Flash::set('Pie de página guardado.');
        Response::redirect('/admin/pie');
    }
}
