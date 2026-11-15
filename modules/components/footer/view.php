<?php use App\Core\Config; ?>
<footer class="ftr">
    <span>© <?= date('Y') ?> <?= e(Config::get('app.name')) ?></span>
    <span><?= e(Config::get('app.tagline')) ?></span>
</footer>
