<?php return [
    'label'      => 'Vídeo de fondo',
    'desc'       => 'Un vídeo corto en bucle (sin sonido) a pantalla completa con tu título encima.',
    'photos'     => 'one',
    'photo_hint' => 'Foto de respaldo: se ve mientras carga el vídeo, con ahorro de datos o si el vídeo no puede reproducirse.',
    'icon'       => '<rect x="2" y="2" width="60" height="36" fill-opacity=".2"/><circle cx="32" cy="20" r="11" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M28.5 14.5v11l9-5.5z"/>',
    'options'    => [
        'file'    => ['label' => 'Vídeo (.mp4 o .webm, máx. 40 MB)', 'type' => 'video', 'default' => '', 'hint' => 'Mejor corto (10–20 s), en bucle y comprimido (1080p, 4–8 MB). Se reproduce sin sonido.'],
        'overlay' => ['label' => 'Oscurecer el vídeo (%)', 'type' => 'range', 'min' => 0, 'max' => 80, 'step' => 5, 'default' => 35, 'hint' => 'Más oscuro = el título se lee mejor.'],
        'mobile'  => ['label' => 'En móvil usar solo la foto (ahorra datos)', 'type' => 'bool', 'default' => '0'],
    ],
];
