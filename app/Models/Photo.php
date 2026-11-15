<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\{Config, Database};

final class Photo
{
    public static function forGallery(int $galleryId): array
    {
        return Database::all(
            'SELECT * FROM photos WHERE gallery_id = ? ORDER BY sort_order, id',
            [$galleryId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM photos WHERE id = ?', [$id]);
    }

    public static function url(string $filename): string
    {
        return Config::get('uploads.url') . '/originals/' . rawurlencode($filename);
    }

    public static function thumbUrl(string $filename): string
    {
        return Config::get('uploads.url') . '/thumbs/' . rawurlencode($filename);
    }
}
