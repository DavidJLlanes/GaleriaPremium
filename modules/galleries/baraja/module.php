<?php return [
    'label' => 'Baraja (cartas que se vuelcan)',
    'desc'  => 'Pila de fotos de pie: la delantera se vuelca sobre su borde y cae plana al suelo mientras haces scroll. Todas con la misma proporción (la de la primera).',
    'icon'  => '<rect x="20" y="3" width="24" height="12" fill-opacity=".12"/><rect x="17" y="8" width="30" height="14" fill-opacity=".28"/><rect x="12" y="15" width="40" height="17" fill-opacity=".6"/><path d="M8 36h48" stroke="currentColor" stroke-width="1.5" fill="none"/>',
    'crop'  => true,      // proporción común: la primera foto la fija y eliges el recorte de las demás (Admin → Recortar)
    'options' => [
        'depth'    => ['type' => 'number', 'default' => 5, 'min' => 2, 'max' => 8, 'label' => 'Cartas de pie visibles detrás de la delantera'],
        'speed'    => ['type' => 'enum', 'default' => 'm', 'choices' => ['s' => 'Lento (más scroll por foto)', 'm' => 'Normal', 'f' => 'Rápido (menos scroll por foto)'], 'label' => 'Scroll necesario para volcar cada foto'],
        'captions' => ['type' => 'bool', 'default' => 1, 'label' => 'Mostrar el nombre de la foto y el contador'],
    ],
];
