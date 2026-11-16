<?php use App\Core\Module; use App\Models\Photo; $p = $photos[0]; ?>
<section class="hx-frame" data-hero style="--ar:<?= max(.5, min(1.9, ($p['width'] ?? 3) / max(1, $p['height'] ?? 2))) ?>">
    <figure class="hxf-fig"><img src="<?= e(Photo::url($p['filename'])) ?>" alt="<?= e($p['title']) ?>" fetchpriority="high"></figure>
    <div class="hxf-copy">
        <p class="hxf-eyebrow"><?= e($copy['eyebrow'] ?? '') ?></p>
        <h1><?= e($copy['title'] ?? '') ?></h1>
        <?= Module::render('components/hero-button', ['href' => '#galerias', 'label' => $copy['cta'] ?? 'Explorar galerías']) ?>
    </div>
</section>
