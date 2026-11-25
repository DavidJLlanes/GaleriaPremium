<?php return [
    'label' => 'Abanico (cartas inclinadas)',
    'desc'  => 'Fotos en abanico: la central de frente y las vecinas inclinadas y atenuadas. Al pulsar la central se abre la foto entera; con flechas, puntos, teclado o deslizando se cambia de carta.',
    'icon'  => '<rect x="22" y="5" width="20" height="32" rx="3"/><rect x="5" y="9" width="17" height="27" rx="3" transform="rotate(-9 13 22)" fill-opacity=".5"/><rect x="42" y="9" width="17" height="27" rx="3" transform="rotate(9 50 22)" fill-opacity=".5"/>',
    'crop'  => true,
    'options' => [
        'spread'   => ['type' => 'enum', 'default' => 'm', 'choices' => ['s' => 'Apretado', 'm' => 'Normal', 'l' => 'Abierto'], 'label' => 'Separación entre cartas'],
        'tilt'     => ['type' => 'enum', 'default' => 'm', 'choices' => ['s' => 'Suave', 'm' => 'Media', 'l' => 'Marcada'], 'label' => 'Inclinación de las vecinas'],
        'loop'     => ['type' => 'bool', 'default' => 1, 'label' => 'Dar la vuelta al llegar al final'],
        'captions' => ['type' => 'bool', 'default' => 1, 'label' => 'Mostrar el título de la foto actual'],
    ],
];
