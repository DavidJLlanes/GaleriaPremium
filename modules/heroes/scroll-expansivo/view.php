<?php use App\Core\Module; use App\Models\Photo; $p = $photos[0]; ?>
<section class="hx-expand" data-hero style="--ar:<?= max(.55, min(1.8, ($p['width'] ?? 3) / max(1, $p['height'] ?? 2))) ?>">
    <div class="hxs-sticky">
        <div class="hxs-frame"><img src="<?= e(Photo::url($p['filename'])) ?>" alt="<?= e($p['title']) ?>" fetchpriority="high"></div>
        <div class="hxs-copy">
            <p class="hxs-eyebrow"><?= e($copy['eyebrow'] ?? '') ?></p>
            <h1><?= e($copy['title'] ?? '') ?></h1>
            <div class="hxs-cta"><?= Module::render('components/hero-button', ['href' => '#galerias', 'label' => $copy['cta'] ?? 'Explorar galerías']) ?></div>
        </div>
        <?= Module::render('components/scroll-hint') ?>
    </div>
</section>
