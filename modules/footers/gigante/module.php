<?php
$fonts = ['' => 'La del nombre de la web (Sitio → Tipografías)'];
foreach (\App\Services\Fonts::catalog() as $id => $f) $fonts[$id] = $f['label'];
return ['label' => 'Gigante', 'desc' => 'El nombre de la web a todo lo ancho como firma monumental; debajo, todo centrado: copyright, redes, menú desplegable (legal y Admin) y botón «Arriba».',
  'icon' => '<text x="32" y="22" text-anchor="middle" font-size="19" font-family="serif" fill="currentColor" letter-spacing="1">LUX</text><rect x="8" y="28" width="48" height=".8" opacity=".3"/>',
  'options' => [
    'font' => ['label' => 'Tipografía de las letras', 'type' => 'select', 'default' => '', 'search' => true, 'choices' => $fonts],
    'color_on' => ['label' => 'Usar un color propio para las letras', 'type' => 'bool', 'default' => '0'],
    'color' => ['label' => 'Color de las letras', 'type' => 'color', 'default' => '#f2efe9'],
    'caps' => ['label' => 'Todo en mayúsculas', 'type' => 'bool', 'default' => '1', 'hint' => 'Con tipografías manuscritas queda mejor desactivado.'],'outline' => ['label' => 'Letras solo con contorno', 'type' => 'bool', 'default' => '0']]];
