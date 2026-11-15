<?php use App\Core\Module; ?>
<section class="hero <?= e($class ?? '') ?>" data-hero>
    <div class="hero-media"><?= $media ?? '' ?></div>
    <div class="hero-shade"></div>
    <?= Module::render('components/hero-copy', $copy ?? []) ?>
    <?= Module::render('components/scroll-hint') ?>
</section>
