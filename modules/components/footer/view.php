<?php use App\Core\Config; use App\Services\Site;
$links = []; foreach (Site::SOCIALS as $k => $label) if (Site::get($k) !== '') $links[$label] = Site::get($k);
$mail = Site::get('email'); ?>
<footer class="ftr">
    <span><?= Site::get('footer_text') !== '' ? e(Site::get('footer_text')) : '© ' . date('Y') . ' ' . e(Config::get('app.name')) ?></span>
    <span class="ftr-tag"><?= e(Config::get('app.tagline')) ?></span>
    <nav class="ftr-links" aria-label="Enlaces del pie">
        <?php foreach ($links as $label => $url): ?><a href="<?= e($url) ?>" target="_blank" rel="noopener me"><?= e($label) ?></a><?php endforeach; ?>
        <?php if ($mail !== ''): ?><a href="mailto:<?= e($mail) ?>">Contacto</a><?php endif; ?>
        <?php if (Site::on('footer_admin')): ?><a href="/admin" rel="nofollow">Admin</a><?php endif; ?>
    </nav>
</footer>
