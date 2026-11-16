<?php use App\Core\Module; use App\Models\Photo; ?>
<section class="hx-mask" data-hero>
    <div class="hxm-inner">
        <p class="hxm-eyebrow"><?= e($copy['eyebrow'] ?? '') ?></p>
        <h1 class="hxm-title" style="background-image:url('<?= e(Photo::url($photos[0]['filename'])) ?>')"><?= e($copy['title'] ?? '') ?></h1>
        <?= Module::render('components/hero-button', ['href' => '#galerias', 'label' => $copy['cta'] ?? 'Explorar galerías']) ?>
    </div>
    <?= Module::render('components/scroll-hint') ?>
</section>
