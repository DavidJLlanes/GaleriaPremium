<?php return [
    'label'   => 'Cortina dividida',
    'desc'    => 'La portada se abre en dos mitades y revela la foto; el título entra después.',
    'photos'  => 'one',
    'icon'    => '<rect x="2" y="2" width="60" height="36" fill-opacity=".18"/><rect x="2" y="2" width="22" height="36"/><rect x="40" y="2" width="22" height="36"/><rect x="26" y="19" width="12" height="3" fill-opacity=".7"/>',
    'options' => [
        'direction' => ['label' => 'Se abre', 'type' => 'select', 'choices' => ['v' => 'Hacia los lados (corte vertical)', 'h' => 'Arriba y abajo (corte horizontal)'], 'default' => 'v'],
    ],
];
