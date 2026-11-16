<?php use App\Core\Module;
$o = $opts ?? []; ?>
<section class="hx-neon" data-hero data-speed="<?= (int) ($o['speed'] ?? 5) ?>" data-repeat="<?= ($o['repeat'] ?? '0') === '1' ? '1' : '0' ?>" style="--hxo-c:<?= e($o['color'] ?? '#ffb45a') ?>">
    <p class="hxo-eyebrow"><?= e($copy['eyebrow'] ?? '') ?></p>
    <h1 class="hxo-title hxo-brand"><?= e(\App\Services\Site::get('name')) ?></h1>
    <svg class="hxo-svg" aria-hidden="true" focusable="false"></svg>
    <div class="hxo-cta"><?= Module::render('components/button', ['href' => '#galerias', 'label' => $copy['cta'] ?? 'Explorar galerías']) ?></div>
    <?= Module::render('components/scroll-hint') ?>
</section>
