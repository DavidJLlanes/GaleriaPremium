<?php return ['label' => 'Contador', 'desc' => 'Un número que sube de 0 a 100 mientras una línea de luz se llena; después, el nombre aparece.',
  'manual' => true, 'icon' => '<text x="10" y="26" font-size="18" font-family="serif" fill="currentColor">72</text><rect x="10" y="31" width="44" height="1.2" opacity=".3"/><rect x="10" y="31" width="30" height="1.2"/>',
  'options' => [
    'name' => ['label' => 'Mostrar el nombre al terminar la cuenta', 'type' => 'bool', 'default' => '0', 'hint' => 'Desactivado: al llegar a 100 la entrada se abre directamente a la portada, sin que el nombre aparezca dos veces.'],'pos' => ['label' => 'Posición del número', 'type' => 'select', 'default' => 'corner', 'choices' => ['corner' => 'Esquina inferior', 'center' => 'Centrado']]]];
