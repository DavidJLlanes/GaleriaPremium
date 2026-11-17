<?php
declare(strict_types=1);
/** Migraciones idempotentes (se ejecutan tras schema.sql en cada instalación/despliegue). Solo añaden columnas que falten. */
return static function (\PDO $pdo): void {
    $has = static function (string $table, string $col) use ($pdo): bool {
        foreach ($pdo->query("PRAGMA table_info($table)") as $c) if ($c['name'] === $col) return true;
        return false;
    };
    $add = [
        ['galleries', 'show_cats',  'INTEGER NOT NULL DEFAULT 1'],   // ¿se muestran las categorías de sus fotos?
        ['photos',    'crop_x',     'REAL'],
        ['photos',    'crop_y',     'REAL'],
        ['photos',    'crop_w',     'REAL'],
        ['photos',    'crop_h',     'REAL'],
        ['photos',    'lqip',       'TEXT'],   // miniatura minúscula (data URI WebP) para la carga con desenfoque a nítido
        ['photos',    'exif',       'TEXT'],   // datos de cámara en JSON (solo fotos subidas con EXIF; sin GPS)
        ['galleries', 'crop_ratio', 'REAL'],
        ['galleries', 'options',    "TEXT NOT NULL DEFAULT '{}'"],
        ['galleries', 'kind',       "TEXT NOT NULL DEFAULT 'gallery'"],   // 'gallery' | 'slider' (los sliders son filas con sus fotos, ratio fijo y opciones en `options`)
        ['galleries', 'featured',   'INTEGER NOT NULL DEFAULT 0'],   // destacada (badge + marco de acento)
        ['galleries', 'lightbox',   "TEXT NOT NULL DEFAULT 'fade'"],
    ];
    foreach ($add as [$t, $c, $def]) if (!$has($t, $c)) $pdo->exec("ALTER TABLE $t ADD COLUMN $c $def");
};
