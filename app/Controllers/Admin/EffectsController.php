<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Services\{Auth, Csrf, Effects, Flash};

/** Admin → Efectos: scroll con inercia, cursor, grano, transiciones, carga de fotos y sonidos. */
final class EffectsController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        Page::render('pages/admin-effects', ['v' => Effects::values(), 'cursors' => Module::all('cursors')], ['title' => 'Efectos', 'nav' => 'efectos'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        Effects::save((array) ($_POST['fx'] ?? []), (array) ($_POST['cursor_opt'] ?? []));
        Flash::set('Efectos guardados.');
        Response::redirect('/admin/efectos');
    }
}
