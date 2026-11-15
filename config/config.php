<?php
// Configuración base. Para sobrescribir en el servidor crea config/config.local.php
$config = [
    'app' => [
        'name'     => 'WarpStudio',
        'tagline'  => 'Fotografía que se siente',
        'base_url' => '',            // vacío = raíz del dominio
        'debug'    => false,
    ],
    'db' => [
        'path' => __DIR__ . '/../storage/galeria.sqlite',
    ],
    'uploads' => [
        'dir'       => __DIR__ . '/../public/uploads',
        'url'       => '/uploads',
        'thumb_w'   => 900,
        'max_bytes' => 40 * 1024 * 1024,
    ],
    // Estilos de galería disponibles (clave => etiqueta). Vista en app/Views/gallery/{clave}.php
    'gallery_styles' => [
        'masonry' => 'Mosaico (masonry)',
    ],
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $config = array_replace_recursive($config, require $local);
}
return $config;
