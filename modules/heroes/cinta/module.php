<?php return [
    'label'   => 'Cinta infinita',
    'desc'    => 'Dos filas de fotos que se desplazan sin fin en sentidos opuestos, con el título en el centro.',
    'photos'  => 'many',
    'icon'    => '<rect x="2" y="2" width="60" height="36" fill-opacity=".08"/><rect x="2" y="5" width="14" height="10" fill-opacity=".5"/><rect x="18" y="5" width="18" height="10" fill-opacity=".35"/><rect x="38" y="5" width="14" height="10" fill-opacity=".5"/><rect x="12" y="25" width="16" height="10" fill-opacity=".4"/><rect x="30" y="25" width="14" height="10" fill-opacity=".55"/><rect x="46" y="25" width="16" height="10" fill-opacity=".35"/><rect x="22" y="18" width="20" height="3"/>',
    'options' => [
        'speed' => ['label' => 'Segundos por vuelta (más = más lento)', 'type' => 'range', 'min' => 20, 'max' => 240, 'step' => 5, 'default' => 90],
    ],
];
