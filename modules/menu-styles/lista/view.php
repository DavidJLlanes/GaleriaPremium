<?php /* Menú «Lista fina». Datos: $galleries, $current. Ítems: clase .mnp-item y --i. */ ?>
<ul class="ml">
    <?php foreach ($galleries as $i => $g): ?>
        <li><a class="mnp-item ml-a" style="--i:<?= (int) $i ?>" href="/galeria/<?= e($g['slug']) ?>"<?= $g['slug'] === $current ? ' aria-current="page"' : '' ?>>
            <span class="ml-t"><?= e($g['title']) ?></span><span class="ml-n"><?= (int) $g['photo_count'] ?></span>
        </a></li>
    <?php endforeach; ?>
</ul>
