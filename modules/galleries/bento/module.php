<?php return [
    'label' => 'Bento (mosaico que se expande)',
    'desc'  => 'Mosaico de tamaños alternos: al pulsar una foto se expande al centro con su título y descripción, sobre un fondo desenfocado.',
    'icon'  => '<rect x="3" y="4" width="36" height="15"/><rect x="43" y="4" width="18" height="15" fill-opacity=".5"/><rect x="3" y="23" width="18" height="15" fill-opacity=".5"/><rect x="25" y="23" width="36" height="15"/>',
    'options' => [
        'pattern'  => ['type' => 'enum', 'default' => 'bento', 'choices' => ['bento' => 'Bento (grandes y pequeñas alternas)', 'even' => 'Uniforme (todas iguales)'], 'label' => 'Disposición del mosaico'],
        'gap'      => ['type' => 'enum', 'default' => 'm', 'choices' => ['s' => 'Pequeña', 'm' => 'Media', 'l' => 'Grande'], 'label' => 'Separación entre fotos'],
        'captions' => ['type' => 'enum', 'default' => 'hover', 'choices' => ['hover' => 'Al pasar el ratón (siempre visible en móvil)', 'always' => 'Siempre visible', 'none' => 'Ocultos'], 'label' => 'Título sobre cada foto'],
    ],
];
