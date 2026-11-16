<?php return ['label' => 'Pluma de luz', 'desc' => 'El título se escribe solo con una pluma de luz cálida, trazo a trazo, cuando la tarjeta aparece. Es el mismo efecto de la portada «Título dibujado con luz».', 'place' => 'over',
    'icon' => '<rect x="4" y="4" width="56" height="32" fill-opacity=".35"/><text x="32" y="26" text-anchor="middle" font-size="15" font-family="serif" fill="none" stroke="currentColor" stroke-width="1" stroke-dasharray="26 8">Aa</text><circle cx="45" cy="19" r="1.8"/>',
    'options' => [
        'color' => ['label' => 'Color de la luz', 'type' => 'color', 'default' => '#ffb45a'],
        'speed' => ['label' => 'Velocidad del trazo (1 lenta – 10 rápida)', 'type' => 'range', 'min' => 1, 'max' => 10, 'step' => 1, 'default' => 5],
    ]];
