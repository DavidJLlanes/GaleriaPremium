<?php return [
    'label'   => 'Linterna',
    'desc'    => 'La foto está a oscuras y solo se ilumina la zona que recorres con el cursor o el dedo.',
    'photos'  => 'one',
    'icon'    => '<rect x="2" y="2" width="60" height="36" fill-opacity=".1"/><circle cx="26" cy="20" r="13" fill-opacity=".55"/><circle cx="26" cy="20" r="7" fill-opacity=".85"/><rect x="40" y="19" width="16" height="3" fill-opacity=".5"/>',
    'options' => [
        'radius' => ['label' => 'Tamaño de la luz', 'type' => 'range', 'min' => 12, 'max' => 50, 'step' => 1, 'default' => 26, 'hint' => 'Porcentaje del lado menor de la pantalla.'],
    ],
];
