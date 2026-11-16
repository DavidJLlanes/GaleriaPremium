<?php /* $f = Footer::data(); $only = null | 'social' | 'extra' para repartirlos entre columnas. */ $only = $only ?? null; ?>
<nav class="ftr-links ftr-i" aria-label="Enlaces del pie">
    <?php if ($only !== 'extra') foreach ($f['links'] as $label => $url): ?><a href="<?= e($url) ?>" target="_blank" rel="noopener me"><?= e($label) ?></a><?php endforeach; ?>
    <?php if ($only !== 'social'): ?>
        <?php if ($f['mail'] !== ''): ?><a href="mailto:<?= e($f['mail']) ?>">Contacto</a><?php endif; ?>
        <?php if ($f['top']): ?><a href="#" class="ftr-top" data-magnet onclick="scrollTo({top:0,behavior:'smooth'});return false">Arriba ↑</a><?php endif; ?>
        <?php if ($f['admin']): ?><a href="/admin" rel="nofollow">Admin</a><?php endif; ?>
    <?php endif; ?>
</nav>
