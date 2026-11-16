<?php use App\Core\Module; Module::use('components/stroke-order');
$o = $opts ?? []; ?>
<section class="hx-light" data-hero data-speed="<?= (int) ($o['speed'] ?? 5) ?>" data-repeat="<?= ($o['repeat'] ?? '0') === '1' ? '1' : '0' ?>" style="--hxz-c:<?= e($o['color'] ?? '#ffb45a') ?>">
    <p class="hxz-eyebrow"><?= e($copy['eyebrow'] ?? '') ?></p>
    <h1 class="hxz-title hxz-brand"><?= e(\App\Services\Site::get('name')) ?></h1>
    <canvas class="hxz-canvas" aria-hidden="true"></canvas>
    <div class="hxz-cta"><?= Module::render('components/hero-button', ['href' => '#galerias', 'label' => $copy['cta'] ?? 'Explorar galerías']) ?></div>
    <?= Module::render('components/scroll-hint') ?>
</section>
