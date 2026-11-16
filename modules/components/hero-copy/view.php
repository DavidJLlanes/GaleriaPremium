<?php use App\Core\{Config, Module}; ?>
<div class="herocopy">
    <p class="herocopy-eyebrow"><?= e($eyebrow ?? 'Portfolio fotográfico') ?></p>
    <h1><?= e($title ?? Config::get('app.tagline')) ?></h1>
    <?= Module::render('components/hero-button', ['href' => '#galerias', 'label' => $cta ?? 'Explorar galerías']) ?>
</div>
