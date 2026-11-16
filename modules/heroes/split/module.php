<?php return [
    'label'    => 'Pantalla dividida',
    'desc'     => 'Texto a un lado y foto al otro, o dos fotos que se reparten la pantalla y se expanden al pasar el cursor.',
    'photos'   => 'many',
    'interval' => true,
    'icon'     => '<rect x="2" y="2" width="30" height="36" fill-opacity=".12"/><rect x="34" y="2" width="28" height="36" fill-opacity=".6"/><rect x="7" y="17" width="18" height="3"/><rect x="7" y="23" width="11" height="2" fill-opacity=".6"/>',
    'options'  => [
        'layout' => ['label' => 'Disposición', 'type' => 'select', 'choices' => ['text' => 'Texto + foto', 'duo' => 'Dos fotos (se expanden al pasar el cursor)'], 'default' => 'text', 'hint' => '«Dos fotos» necesita al menos 2 fotos; si hay una sola se usa «Texto + foto».'],
    ],
];
