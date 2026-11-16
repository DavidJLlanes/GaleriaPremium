<?php /* Menú «Títulos grandes». Datos: $galleries, $current, $opts (numbers). */ ?>
<ol class="mg">
    <?php foreach ($galleries as $i => $g): ?>
        <li><a class="mnp-item mg-a" style="--i:<?= (int) $i ?>" href="/galeria/<?= e($g['slug']) ?>"<?= $g['slug'] === $current ? ' aria-current="page"' : '' ?>>
            <?php if (($opts['numbers'] ?? '1') === '1'): ?><span class="mg-i"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span><?php endif; ?>
            <span class="mg-t"><?= e($g['title']) ?></span>
        </a></li>
    <?php endforeach; ?>
</ol>
