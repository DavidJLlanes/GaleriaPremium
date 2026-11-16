<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Module, Page, Response};
use App\Services\{Auth, Csrf, Flash, Site, SiteMenu};

/** Admin → Barra y menú: la barra superior (dónde se ve, marca y colores) y su menú de galerías (icono «hamburguesa», despliegue y diseño). */
final class MenuController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        $kinds = [];
        foreach (SiteMenu::KINDS as $k => $dir) $kinds[$k] = Module::all($dir);
        $f = [];
        foreach (Site::BAR_KEYS as $k) $f[$k] = Site::get($k);
        Page::render('pages/admin-menu', ['m' => SiteMenu::values(), 'kinds' => $kinds, 'f' => $f, 'logo' => Site::brandUrl('logo.webp')], ['title' => 'Barra y menú', 'nav' => 'menu'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        Site::save($_POST, true);
        SiteMenu::save($_POST);
        Flash::set('Barra y menú guardados.');
        Response::redirect('/admin/menu');
    }
}
