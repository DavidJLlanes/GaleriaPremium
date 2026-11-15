<?php
// Datos de ejemplo (temporales). Fotos en database/demo/photos/ (ya en WebP 75%).
return [
    'hero' => ['type' => 'gallery', 'gallery' => 'otono'],   // o ['type' => 'single', 'photo' => 'leon-02']
    'galleries' => [
        ['slug' => 'otono', 'title' => 'Otoño en León', 'style' => 'masonry',
         'description' => 'Hojas, paseos y luz cálida de noviembre en el parque.',
         'photos' => [
             'otono-01' => 'Ventana al parque', 'otono-02' => 'Paseo de plátanos', 'otono-03' => 'El banco de León',
             'otono-04' => 'Farola entre hojas', 'otono-05' => 'Alfombra de hojas']],
        ['slug' => 'reflejos', 'title' => 'Reflejos', 'style' => 'filmstrip',
         'description' => 'El mundo del revés: agua, cielo y árboles.',
         'photos' => [
             'reflejos-01' => 'La fuente en el espejo', 'reflejos-02' => 'Charco y cielo',
             'reflejos-03' => 'Hojas sobre el agua', 'reflejos-04' => 'Río en calma']],
        ['slug' => 'fauna', 'title' => 'Fauna del parque', 'style' => 'grid',
         'description' => 'Patos y compañía junto al estanque.',
         'photos' => [
             'fauna-01' => 'Pato criollo', 'fauna-02' => 'Ánade azulón',
             'fauna-03' => 'Reflejos rosados', 'fauna-04' => 'Estirando las alas']],
        ['slug' => 'troncos', 'title' => 'Troncos y raíces', 'style' => 'justified',
         'description' => 'Texturas de corteza y gigantes silenciosos.',
         'photos' => [
             'troncos-01' => 'Gigantes', 'troncos-02' => 'El álamo', 'troncos-03' => 'Raíces', 'troncos-04' => 'Horquilla']],
        ['slug' => 'leon', 'title' => 'León y Michi', 'style' => 'masonry',
         'description' => 'La ciudad, su arquitectura y un gato viajero.',
         'photos' => ['leon-01' => 'Casa Botines', 'leon-02' => 'Planeta León', 'leon-03' => 'Retrato']],
    ],
];
