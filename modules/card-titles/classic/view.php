<?php /* Título de tarjeta «Clásico». Datos: $g (galería), $n (posición), $opts. */ ?>
<div class="ct-classic">
    <span class="ct-classic-count"><?= (int) $g['photo_count'] ?> fotos</span>
    <h2><?= e($g['title']) ?></h2>
    <?php if ($g['description'] !== ''): ?><p><?= e($g['description']) ?></p><?php endif; ?>
</div>
