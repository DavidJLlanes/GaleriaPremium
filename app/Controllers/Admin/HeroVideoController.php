<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Response;
use App\Services\{Auth, Csrf, HeroVideo};

/** Admin → Portada → hero «video»: subida del vídeo de fondo (JSON). Se aplica al pulsar «Guardar portada». */
final class HeroVideoController
{
    public function upload(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        try { $name = HeroVideo::store($_FILES['video'] ?? []); }
        catch (\RuntimeException $e) { Response::json(['ok' => false, 'error' => $e->getMessage()], 422); }
        Response::json(['ok' => true, 'file' => $name, 'url' => HeroVideo::url($name), 'size' => (int) $_FILES['video']['size']]);
    }
}
