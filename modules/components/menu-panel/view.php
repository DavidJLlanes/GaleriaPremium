<?php /* Panel del menú de galerías. Va a nivel de <body> (no dentro de la barra, que tiene backdrop-filter). Datos: $m = SiteMenu::values(), $galleries. */
use App\Core\Module;
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$current = str_starts_with($path, '/galeria/') ? rawurldecode(substr($path, 9)) : '';
Module::use('menu-reveals/' . $m['reveal']);
?>
<div class="mnp rv-<?= e($m['reveal']) ?>" id="mnPanel" role="dialog" aria-modal="true" aria-label="Galerías" inert>
    <div class="mnp-back" data-mn-close></div>
    <div class="mnp-sheet"><div class="mnp-in">
        <?= Module::render('menu-styles/' . $m['style'], ['galleries' => $galleries, 'current' => $current, 'opts' => $m['opts']['style']]) ?>
    </div></div>
</div>
