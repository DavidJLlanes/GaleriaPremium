<?php return [
    'label'   => 'Título en neón',
    'desc'    => 'Fondo negro: una luz recorre el contorno de cada letra como un neón que se enciende (con tu tipografía).',
    'photos'  => 'none',
    'icon'    => '<rect x="2" y="2" width="60" height="36" fill="#000" fill-opacity=".5"/><text x="32" y="28" text-anchor="middle" font-size="22" font-family="serif" fill="none" stroke="currentColor" stroke-width="1.1" stroke-dasharray="40 16">Aa</text><circle cx="42" cy="12" r="2.2"/>',
    'options' => [
        'source' => ['label' => 'Texto que se dibuja', 'type' => 'select', 'choices' => ['site' => 'Nombre de la web', 'hero' => 'Título grande de Portada'], 'default' => 'site'],
        'color'  => ['label' => 'Color de la luz', 'type' => 'color', 'default' => '#ffb45a'],
        'speed'  => ['label' => 'Velocidad del trazo (1 lenta – 10 rápida)', 'type' => 'range', 'min' => 1, 'max' => 10, 'step' => 1, 'default' => 5],
        'repeat' => ['label' => 'Repetir la animación en bucle', 'type' => 'bool', 'default' => '0'],
    ],
];
