<?php /* Datos: $g (slider), $cfg (configuración), $photos, $ratio (proporción fija). */
use App\Core\Module; use App\Services\Slider;
Module::use('components/slider-engine'); $fxOn = true; if ($fxOn) Module::use('slider-fx/' . $cfg['fx']);
$d = ['g' => $g, 'cfg' => $cfg, 'ratio' => $ratio]; $n = count($photos); ?>
<div <?= Slider::attrs($d, 'stack', 'miniaturas') ?>>
    <?php if ($cfg['title'] === '1'): ?><h2 class="sl-title"><?= e($g['title']) ?></h2><?php endif; ?>
    <div class="sl-stage" data-lightbox data-lbfx="<?= e($g['lightbox'] ?? 'fade') ?>">
        <?php foreach ($photos as $i => $p) echo Module::render('components/slider-slide', ['p' => $p, 'i' => $i, 'captions' => $cfg['captions'] === '1']); ?>
        <?= Module::render('components/slider-controls', ['cfg' => $cfg, 'n' => $n, 'dots' => false]) ?>
    </div>
    <div class="sl-thumbs" role="tablist" aria-label="Miniaturas">
        <?php foreach ($photos as $i => $p): ?><button type="button" class="sl-th" data-sl-thumb="<?= $i ?>" aria-label="Foto <?= $i + 1 ?>"><img src="<?= e(\App\Models\Photo::thumbUrl($p['filename'])) ?>" alt="" loading="lazy" draggable="false"></button><?php endforeach; ?>
    </div>
</div>
