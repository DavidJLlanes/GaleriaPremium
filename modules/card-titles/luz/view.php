<?php /* Título de tarjeta «Pluma de luz»: usa el motor components/light-pen (el mismo del hero «Título dibujado con luz»). */
use App\Core\Module; Module::use('components/stroke-order'); Module::use('components/light-pen');
$o = $opts ?? []; ?>
<div class="ct-luz" data-speed="<?= (int) ($o['speed'] ?? 5) ?>" style="--ctl-c:<?= e($o['color'] ?? '#ffb45a') ?>">
    <span class="ct-luz-count"><?= (int) $g['photo_count'] ?> fotos</span>
    <h2 class="ct-luz-title"><?= e($g['title']) ?></h2>
    <canvas class="ct-luz-canvas" aria-hidden="true"></canvas>
</div>
<noscript><style>.ct-luz-title{opacity:1!important}</style></noscript>
