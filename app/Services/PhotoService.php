<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Config;
use App\Models\{Gallery, Photo};
use App\Services\Ai\{AiException, AiService};

/** Flujo completo de subida: validar → WebP 75% → miniatura → IA opcional → guardar. */
final class PhotoService
{
    private const MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * @param array $file  entrada de $_FILES
     * @return array  fila de la foto + 'ai' => ['used'=>bool,'error'=>?string]
     */
    public static function store(array $file, int $galleryId, string $title, string $description, bool $useAi): array
    {
        $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($err !== UPLOAD_ERR_OK) throw new \RuntimeException(self::uploadError((int) $err));
        $tmp = $file['tmp_name'];
        if (!is_uploaded_file($tmp) && !is_file($tmp)) throw new \RuntimeException('Archivo no válido');
        if (filesize($tmp) > (int) Config::get('uploads.max_bytes')) throw new \RuntimeException('El archivo supera el tamaño máximo');

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if (!in_array($mime, self::MIMES, true) || !@getimagesize($tmp)) {
            throw new \RuntimeException('Formato no soportado (usa JPG, PNG, WebP o GIF)');
        }

        // Una imagen pequeña en bytes puede tener decenas de miles de píxeles de lado y agotar la memoria al decodificarla
        [$iw, $ih] = (array) @getimagesize($tmp) + [0, 0];
        if ($iw < 1 || $ih < 1 || $iw * $ih > 80_000_000) throw new \RuntimeException('La imagen tiene demasiados píxeles (máximo 80 megapíxeles).');

        $title = trim($title); $description = trim($description);
        $original = pathinfo((string) ($file['name'] ?? ''), PATHINFO_FILENAME);

        $dir = rtrim(Config::get('uploads.dir'), '/');
        @mkdir("$dir/originals", 0775, true); @mkdir("$dir/thumbs", 0775, true);
        $base = Gallery::slugify($title !== '' ? $title : $original) . '-' . bin2hex(random_bytes(3));
        $name = $base . '.webp';

        $exif = ImageProcessor::exif($tmp);   // se lee del original: el WebP no conserva EXIF
        // REGLA: WebP calidad 75
        [$w, $h] = ImageProcessor::convert($tmp, "$dir/originals/$name");
        ImageProcessor::thumbnail("$dir/originals/$name", "$dir/thumbs/$name");
        $lqip = ImageProcessor::placeholder("$dir/thumbs/$name");

        // IA solo para lo que falta (título y/o descripción vacíos)
        $ai = ['used' => false, 'error' => null];
        if ($useAi && ($title === '' || $description === '')) {
            if (!AiService::isConfigured()) {
                $ai['error'] = 'IA no configurada (Ajustes → Inteligencia artificial)';
            } else {
                try {
                    $r = AiService::describe("$dir/originals/$name", $title);
                    if ($title === '') $title = $r['title'];
                    if ($description === '') $description = $r['description'];
                    $ai['used'] = true;
                } catch (AiException $e) {
                    $ai['error'] = $e->getMessage();
                }
            }
        }
        if ($title === '') $title = ucfirst(trim(preg_replace('/[_\-]+/', ' ', $original))) ?: 'Sin título';

        $id = Photo::create($galleryId, $name, $title, $description, $w, $h, $lqip, $exif ? json_encode($exif, JSON_UNESCAPED_UNICODE) : null);
        return (Photo::find($id) ?? []) + ['ai' => $ai];
    }

    private static function uploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo es demasiado grande (límite de PHP/Nginx)',
            UPLOAD_ERR_PARTIAL => 'Subida incompleta, inténtalo de nuevo',
            UPLOAD_ERR_NO_FILE => 'No se recibió ningún archivo',
            default => 'Error al subir el archivo',
        };
    }
}
