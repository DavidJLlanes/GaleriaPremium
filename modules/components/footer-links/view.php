<?php /* $f = Footer::data(); $only = null | 'social' | 'extra' para repartirlos entre columnas. */ $only = $only ?? null; ?>
<nav class="ftr-links ftr-i" aria-label="Enlaces del pie">
    <?php if ($only !== 'extra' && $f['links']) echo \App\Core\Module::render('components/social-icons', ['items' => $f['links']]); ?>
    <?php if ($only !== 'social'): ?>
        <?php if ($f['mail'] !== ''): ?><a href="mailto:<?= e($f['mail']) ?>">Contacto</a><?php endif; ?>
        <?php if ($f['top']): ?><a href="#" class="ftr-top" data-magnet>Arriba ↑</a><?php endif; ?>
        <?php if ($f['admin']): ?><a href="/admin" rel="nofollow">Admin</a><?php endif; ?>
    <?php endif; ?>
</nav>
