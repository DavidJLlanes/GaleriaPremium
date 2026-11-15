<?php use App\Core\Config; ?>
<footer class="ftr">
    <span>© <?= date('Y') ?> <?= e(Config::get('app.name')) ?></span>
    <span class="ftr-tag"><?= e(Config::get('app.tagline')) ?></span>
    <nav class="ftr-links" aria-label="Enlaces del pie">
        <a href="/admin" rel="nofollow">Admin</a>
    </nav>
</footer>
