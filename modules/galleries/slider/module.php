<?php return [
    'label' => 'Slider horizontal (mismo recorte)',
    'desc'  => 'Fotos con la misma proporción en un slider. La primera fija la proporción y eliges el recorte de las demás.',
    'icon'  => '<rect x="2" y="9" width="17" height="22"/><rect x="23" y="9" width="17" height="22"/><rect x="44" y="9" width="17" height="22" fill-opacity=".45"/>',
    'crop'  => true,      // este diseño usa recortes con proporción común (Admin → Recortar)
    'options' => [
        'fit'      => ['type' => 'bool',   'default' => 1,   'label' => 'Ajustar la separación entre fotos al ancho de la pantalla (nunca se ve una foto cortada)'],
        'spacing'  => ['type' => 'enum',   'default' => 'm', 'choices' => ['none' => 'Sin separación', 's' => 'Pequeña', 'm' => 'Media', 'l' => 'Grande'], 'label' => 'Separación entre fotos (se adapta sola al móvil y al escritorio)'],
        'size'     => ['type' => 'enum',   'default' => 'm', 'choices' => ['s' => 'Pequeñas', 'm' => 'Medianas', 'l' => 'Grandes'], 'label' => 'Tamaño de las fotos'],
        'captions' => ['type' => 'bool',   'default' => 1,   'label' => 'Mostrar el nombre bajo cada foto'],
    ],
];
