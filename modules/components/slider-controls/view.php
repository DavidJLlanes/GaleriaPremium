<?php /* $cfg = configuración; $n = nº de fotos; $arrows / $dots = false para omitir (diseños que tienen los suyos). */
use App\Core\Module;
if (($n ?? 0) < 2) return;
if (($arrows ?? true) && $cfg['arrows'] !== 'none') echo Module::render('slider-arrows/' . $cfg['arrows'], ['n' => $n]);
if (($dots ?? true) && $cfg['dots'] !== 'none') echo Module::render('slider-dots/' . $cfg['dots'], ['n' => $n]);
