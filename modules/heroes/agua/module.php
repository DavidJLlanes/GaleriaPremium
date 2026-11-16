<?php return [
    'label'   => 'Agua (ondas)',
    'desc'    => 'La foto ondula como el agua al mover el ratón o tocar la pantalla, y de vez en cuando cae una gota sola.',
    'photos'  => 'one',
    'icon'    => '<rect x="2" y="2" width="60" height="36" fill-opacity=".25"/><circle cx="32" cy="20" r="4" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="32" cy="20" r="9" fill="none" stroke="currentColor" stroke-width="1.2" stroke-opacity=".7"/><circle cx="32" cy="20" r="15" fill="none" stroke="currentColor" stroke-width="1" stroke-opacity=".4"/>',
    'options' => [
        'strength' => ['label' => 'Intensidad de las ondas', 'type' => 'range', 'min' => 0.5, 'max' => 2.5, 'step' => 0.1, 'default' => 1.2],
    ],
];
