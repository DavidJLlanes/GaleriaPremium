<?php use App\Core\{Config, Module}; use App\Models\Photo;
$p = $photos[0]; $st = $stats ?? [];
$total = static fn (int $n, string $one, string $many) => $n . ' ' . ($n === 1 ? $one : $many); ?>
<section class="hx-edit" data-hero>
    <header class="hxe-top">
        <span><?= e(Config::get('app.name')) ?></span>
        <span><?= e($total((int) ($st['galleries'] ?? 0), 'galería', 'galerías')) ?> · <?= e($total((int) ($st['photos'] ?? 0), 'fotografía', 'fotografías')) ?></span>
    </header>
    <figure class="hxe-photo">
        <img src="<?= e(Photo::url($p['filename'])) ?>" alt="<?= e($p['title']) ?>" fetchpriority="high">
        <figcaption>Fig. 01 — <?= e($p['title']) ?></figcaption>
    </figure>
    <div class="hxe-copy">
        <p class="hxe-eyebrow"><?= e($copy['eyebrow'] ?? '') ?></p>
        <h1><?= e($copy['title'] ?? '') ?></h1>
        <?= Module::render('components/button', ['href' => '#galerias', 'label' => $copy['cta'] ?? 'Explorar galerías']) ?>
    </div>
    <span class="hxe-year">© <?= date('Y') ?></span>
</section>
