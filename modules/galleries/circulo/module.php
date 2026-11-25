<?php return [
    'label' => 'Círculo (la foto brota de un punto)',
    'desc'  => 'Marco con puntos-miniatura abajo: al elegir una foto, su punto crece como un círculo hasta cubrirlo todo y la anterior rebota hasta su punto. Un clic en la foto abre la foto entera.',
    'icon'  => '<rect x="10" y="3" width="44" height="36" rx="5" fill-opacity=".35"/><circle cx="32" cy="19" r="10"/><circle cx="20" cy="33" r="2"/><circle cx="26" cy="33" r="2"/><circle cx="32" cy="33" r="2"/><circle cx="38" cy="33" r="2"/><circle cx="44" cy="33" r="2"/>',
    'crop'  => true,
    'options' => [
        'autoplay' => ['type' => 'enum', 'default' => 'off', 'choices' => ['off' => 'Sin pase automático', '4' => 'Cada 4,5 s', '7' => 'Cada 7 s'], 'label' => 'Pase automático'],
        'captions' => ['type' => 'bool', 'default' => 1, 'label' => 'Mostrar el título de la foto actual'],
    ],
];
