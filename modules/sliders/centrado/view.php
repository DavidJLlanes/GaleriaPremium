<?php /* Datos: $g (slider), $cfg (configuración), $photos, $ratio (proporción fija). */
use App\Core\Module; use App\Services\Slider;
Module::use('components/slider-engine'); $fxOn = false; if ($fxOn) Module::use('slider-fx/' . $cfg['fx']);
$d = ['g' => $g, 'cfg' => $cfg, 'ratio' => $ratio]; $n = count($photos); ?>
<div <?= Slider::attrs($d, 'coverflow', 'centrado') ?>>
    <?php if ($cfg['title'] === '1'): ?><h2 class="sl-title"><?= e($g['title']) ?></h2><?php endif; ?>
    <div class="sl-view" data-lightbox data-lbfx="<?= e($g['lightbox'] ?? 'fade') ?>">
        <div class="sl-cf">
            <?php foreach ($photos as $i => $p) echo Module::render('components/slider-slide', ['p' => $p, 'i' => $i, 'captions' => $cfg['captions'] === '1']); ?>
        </div>
        <?= Module::render('components/slider-controls', ['cfg' => $cfg, 'n' => $n]) ?>
    </div>
</div>
