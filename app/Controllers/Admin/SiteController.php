<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Page, Response};
use App\Services\{Auth, Csrf, Flash, Site};
use App\Services\Brand\IconBuilder;
use App\Services\ImageProcessor;

/** Admin → Sitio: nombre, marca (logo, icono, favicon), colores, webapp, redes y SEO. */
final class SiteController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        $f = [];
        foreach (array_keys(Site::FIELDS) as $k) $f[$k] = Site::get($k);
        Page::render('pages/admin-site', [
            'f' => $f, 'langs' => Site::LANGS, 'socials' => Site::SOCIALS,
            'logo' => Site::brandUrl('logo.webp'), 'icon' => Site::brandUrl('icon-512.png'),
            'favicon' => Site::brandUrl('favicon-32.png'), 'hasFaviconSrc' => Site::hasBrand('favicon-source.png'),
            'ogImage' => Site::brandUrl('og.webp'),
            'apple' => Site::brandUrl('apple-touch-icon.png'), 'maskable' => Site::brandUrl('maskable-512.png'),
        ], ['title' => 'Sitio', 'nav' => 'sitio'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        Site::save($_POST);
        $errors = [];

        foreach (['logo' => 'setLogo', 'icon' => 'setIcon', 'favicon' => 'setFavicon'] as $field => $method) {
            if (isset($_POST["clear_$field"])) { IconBuilder::clear($field); continue; }
            $f = $_FILES[$field] ?? null;
            if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
            if ($f['error'] !== UPLOAD_ERR_OK) { $errors[] = ucfirst($field) . ': no se pudo subir el archivo (¿demasiado grande?).'; continue; }
            if ($f['size'] > 40 * 1024 * 1024) { $errors[] = ucfirst($field) . ': el archivo supera 40 MB.'; continue; }
            try { IconBuilder::$method($f['tmp_name']); }
            catch (\Throwable $e) { $errors[] = ucfirst($field) . ': ' . $e->getMessage(); }
        }
        if (isset($_POST['clear_og'])) @unlink(Site::brandFile('og.webp'));
        $og = $_FILES['og_image'] ?? null;
        if ($og && ($og['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if ($og['error'] !== UPLOAD_ERR_OK || $og['size'] > 40 * 1024 * 1024) $errors[] = 'Imagen Open Graph: archivo no válido o superior a 40 MB.';
            else {
                $crop = json_decode((string) ($_POST['og_crop'] ?? ''), true);
                if (!is_array($crop)) $errors[] = 'Imagen Open Graph: elige el recorte antes de guardar.';
                else try { ImageProcessor::cropTo($og['tmp_name'], Site::brandFile('og.webp'), $crop, 1200, 630); }
                catch (\Throwable $e) { $errors[] = 'Imagen Open Graph: ' . $e->getMessage(); }
            }
        }
        // El color del tema también tiñe los iconos de iOS/Android: se regeneran siempre
        try { IconBuilder::rebuild(); } catch (\Throwable) {}

        if ($errors) Flash::set(implode(' ', $errors), 'error'); else Flash::set('Ajustes del sitio guardados.');
        Response::redirect('/admin/sitio');
    }
}
