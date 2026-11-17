<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

use App\Controllers\{HomeController, GalleryController, BrandController};
use App\Controllers\Admin\{AuthController, GalleryController as AdminGallery, PhotoController, SettingsController, SiteController, HeroController};
use App\Core\Router;

\App\Services\Site::boot();   // nombre y eslogan guardados en Admin → Sitio
$router = new Router();
$router->get('/', [new HomeController(), 'index']);
$router->get('/galeria/{slug}', [new GalleryController(), 'show']);
$router->get('/manifest.webmanifest', [new BrandController(), 'manifest']);
$router->get('/browserconfig.xml', [new BrandController(), 'browserconfig']);

// ---- Panel de administración ----
$auth = new AuthController(); $ag = new AdminGallery(); $ph = new PhotoController(); $st = new SettingsController(); $site = new SiteController(); $hero = new HeroController();
$router->get('/admin',                       [$ag, 'index']);
$router->get('/admin/login',                 [$auth, 'form']);
$router->post('/admin/login',                [$auth, 'login']);
$router->post('/admin/logout',               [$auth, 'logout']);
$pk = new App\Controllers\Admin\PasskeyController();
$router->get('/admin/acceso',                [$pk, 'page']);
$router->post('/admin/passkey/entrar/opciones', [$pk, 'loginOptions']);
$router->post('/admin/passkey/entrar',       [$pk, 'login']);
$router->post('/admin/passkey/registro/opciones', [$pk, 'registerOptions']);
$router->post('/admin/passkey/registro',     [$pk, 'register']);
$router->post('/admin/passkey/{id}/borrar',  [$pk, 'delete']);
$router->post('/admin/galerias',             [$ag, 'create']);
$router->post('/admin/galerias/orden',       [$ag, 'reorderAll']);
$router->post('/admin/galeria/{id}/marca',   [$ag, 'flag']);
$router->post('/admin/galerias/borrar',      [$ag, 'bulkDelete']);
$router->post('/admin/fotos/borrar',         [$ph, 'bulkDelete']);
$router->post('/admin/fotos/mover',          [$ph, 'move']);
$router->get('/admin/fotos',                [new App\Controllers\Admin\PhotosController(), 'index']);
$router->get('/admin/sitio',                 [$site, 'show']);
$router->post('/admin/sitio',                [$site, 'save']);
$router->post('/admin/fuentes/subir',        [$fonts = new App\Controllers\Admin\FontController(), 'upload']);
$router->post('/admin/fuentes/borrar',       [$fonts, 'delete']);
$router->get('/admin/tarjetas',               [$cards = new App\Controllers\Admin\CardsController(), 'show']);
$router->post('/admin/tarjetas',              [$cards, 'save']);
$router->get('/admin/pie',                    [$ftr = new App\Controllers\Admin\FooterController(), 'show']);
$router->post('/admin/pie',                   [$ftr, 'save']);
$router->get('/admin/entrada',                [$intro = new App\Controllers\Admin\IntroController(), 'show']);
$router->post('/admin/entrada',               [$intro, 'save']);
$router->get('/admin/efectos',                [$fx = new App\Controllers\Admin\EffectsController(), 'show']);
$router->post('/admin/efectos',               [$fx, 'save']);
$router->get('/admin/menu',                   [$menu = new App\Controllers\Admin\MenuController(), 'show']);
$router->post('/admin/menu',                  [$menu, 'save']);
$router->get('/admin/portada',               [$hero, 'show']);
$router->post('/admin/portada',              [$hero, 'save']);
$router->post('/admin/portada/video',        [new App\Controllers\Admin\HeroVideoController(), 'upload']);
$sl = new \App\Controllers\Admin\SliderController();
$ct = new \App\Controllers\Admin\CategoriesController();
$router->get('/admin/categorias', [$ct, 'index']);
$router->post('/admin/categorias', [$ct, 'save']);
$router->post('/admin/categorias/crear', [$ct, 'create']);
$router->post('/admin/categorias/unir', [$ct, 'merge']);
$router->post('/admin/categorias/limpiar', [$ct, 'clean']);
$router->post('/admin/categorias/fotos', [$ct, 'photos']);
$router->get('/admin/categoria/{id}', [$ct, 'show']);
$router->post('/admin/categoria/{id}/mover', [$ct, 'moveAll']);
$router->post('/admin/categoria/{id}/renombrar', [$ct, 'rename']);
$router->post('/admin/categoria/{id}/borrar', [$ct, 'delete']);
$router->get('/admin/sliders', [$sl, 'index']);
$router->post('/admin/sliders', [$sl, 'create']);
$router->get('/admin/slider/{id}', [$sl, 'show']);
$router->post('/admin/slider/{id}/editar', [$sl, 'update']);
$router->post('/admin/slider/{id}/borrar', [$sl, 'delete']);
$router->get('/admin/galeria/{id}',          [$ag, 'show']);
$router->post('/admin/galeria/{id}/editar',  [$ag, 'update']);
$router->post('/admin/galeria/{id}/borrar',  [$ag, 'delete']);
$router->post('/admin/galeria/{id}/subir',   [$ag, 'upload']);
$router->post('/admin/galeria/{id}/orden',   [$ag, 'reorder']);
$router->post('/admin/foto/{id}/editar',     [$ph, 'update']);
$router->post('/admin/foto/{id}/ia',         [$ph, 'ai']);
$router->post('/admin/foto/{id}/recorte',    [$ph, 'crop']);
$router->post('/admin/foto/{id}/portada',    [$ph, 'cover']);
$router->post('/admin/foto/{id}/borrar',     [$ph, 'delete']);
$router->get('/admin/ajustes',               [$st, 'show']);
$router->post('/admin/ajustes',              [$st, 'save']);
$router->post('/admin/ajustes/probar',       [$st, 'test']);
$router->post('/admin/ajustes/modelos',      [$st, 'models']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
