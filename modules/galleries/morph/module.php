<?php return [
    'label' => 'Morph (disolución por ruido)',
    'desc'  => 'Pase de fotos a pantalla ancha: cada foto se disuelve en la siguiente con un shader WebGL (las zonas claras de la nueva aparecen primero). Sin WebGL se desvanece.',
    'icon'  => '<rect x="3" y="4" width="58" height="34"/><circle cx="22" cy="18" r="9" fill-opacity=".45"/><circle cx="40" cy="24" r="11" fill-opacity=".25"/>',
    'options' => [
        'duration'   => ['type' => 'enum', 'default' => 'm', 'choices' => ['s' => 'Rápida (1 s)', 'm' => 'Media (1,5 s)', 'l' => 'Lenta (2,4 s)'], 'label' => 'Duración de la disolución'],
        'noise'      => ['type' => 'enum', 'default' => 'm', 'choices' => ['s' => 'Jirones grandes', 'm' => 'Medios', 'l' => 'Jirones finos'], 'label' => 'Tamaño del ruido'],
        'autoplay'   => ['type' => 'enum', 'default' => 'off', 'choices' => ['off' => 'Sin pase automático', '4' => 'Cada 4 s', '6' => 'Cada 6 s', '9' => 'Cada 9 s'], 'label' => 'Pase automático'],
        'thumbs'     => ['type' => 'bool', 'default' => 1, 'label' => 'Tira de miniaturas (solo escritorio)'],
        'captions'   => ['type' => 'bool', 'default' => 1, 'label' => 'Mostrar título y descripción'],
    ],
];
