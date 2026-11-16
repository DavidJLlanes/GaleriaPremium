<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Services\{Auth, Csrf, Flash, SiteMenu};

/** Admin → Menú: icono («hamburguesa»), efecto de despliegue y diseño del menú de galerías de la barra superior. */
final class MenuController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        $kinds = [];
        foreach (SiteMenu::KINDS as $k => $dir) $kinds[$k] = Module::all($dir);
        Page::render('pages/admin-menu', ['m' => SiteMenu::values(), 'kinds' => $kinds], ['title' => 'Menú', 'nav' => 'menu'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        SiteMenu::save($_POST);
        Flash::set('Menú guardado.');
        Response::redirect('/admin/menu');
    }
}
