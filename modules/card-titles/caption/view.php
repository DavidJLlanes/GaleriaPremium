<?php /* Título de tarjeta «Bajo la foto». */ ?>
<div class="ct-caption">
    <h2><?= e($g['title']) ?></h2>
    <span><?= (int) $g['photo_count'] ?> fotos</span>
    <?php if ($g['description'] !== ''): ?><p><?= e($g['description']) ?></p><?php endif; ?>
</div>
