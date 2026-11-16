<?php /* Título de tarjeta «Numerado». $n = posición de la galería. */ ?>
<div class="ct-numbered">
    <b aria-hidden="true"><?= str_pad((string) ($n ?? 1), 2, '0', STR_PAD_LEFT) ?></b>
    <div><h2><?= e($g['title']) ?></h2><small><?= (int) $g['photo_count'] ?> fotos</small></div>
</div>
