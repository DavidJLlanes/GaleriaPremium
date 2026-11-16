<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Services\{Auth, Csrf, Flash, Intro};

/** Admin → Entrada: pantalla de entrada breve (diseño, texto, tipografía en Sitio, colores, duración, frecuencia). */
final class IntroController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        Page::render('pages/admin-intro', ['v' => Intro::values(), 'designs' => Module::all('intros')], ['title' => 'Entrada', 'nav' => 'entrada'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        Intro::save((array) ($_POST['in'] ?? []), (array) ($_POST['in_opt'] ?? []));
        Flash::set('Pantalla de entrada guardada.');
        Response::redirect('/admin/entrada');
    }
}
