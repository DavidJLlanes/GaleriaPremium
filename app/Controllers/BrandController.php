<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Services\Site;

/** Ficheros de webapp que deben ser dinámicos: manifest y browserconfig. */
final class BrandController
{
    public function manifest(array $p = []): void
    {
        $icons = [];
        foreach ([['icon-192.png', 192, 'any'], ['icon-512.png', 512, 'any'], ['maskable-512.png', 512, 'maskable']] as [$file, $size, $purpose]) {
            if (Site::hasBrand($file)) $icons[] = ['src' => '/uploads/brand/' . $file . '?v=' . (int) @filemtime(Site::brandFile($file)), 'sizes' => "{$size}x{$size}", 'type' => 'image/png', 'purpose' => $purpose];
        }
        header('Content-Type: application/manifest+json; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
        echo json_encode([
            'id' => '/', 'name' => Site::get('name'), 'short_name' => Site::shortName(),
            'description' => Site::get('description') ?: Site::get('tagline'),
            'start_url' => '/', 'scope' => '/', 'display' => Site::get('display'), 'orientation' => 'any',
            'lang' => Site::get('lang'), 'background_color' => Site::get('bg'), 'theme_color' => Site::get('theme'),
            'icons' => $icons,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    public function browserconfig(array $p = []): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $tile = Site::hasBrand('mstile-150.png') ? '<square150x150logo src="/uploads/brand/mstile-150.png"/>' : '';
        echo '<?xml version="1.0" encoding="utf-8"?><browserconfig><msapplication><tile>' . $tile
           . '<TileColor>' . htmlspecialchars(Site::get('theme')) . '</TileColor></tile></msapplication></browserconfig>';
        exit;
    }
}
