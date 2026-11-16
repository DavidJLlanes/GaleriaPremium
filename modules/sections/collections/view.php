<?php use App\Core\Module; use App\Services\Cards;
$c = Cards::values();
Module::use('reveals/' . $c['reveal']); Module::use('card-titles/' . $c['style']);
Module::use('components/card-reveal'); ?>
<section class="coll" id="galerias" data-rv="<?= e($c['reveal']) ?>" data-parallax="<?= $c['parallax'] ? '1' : '0' ?>">
    <?php if ($c['reveal'] !== 'none'): ?><script>document.currentScript.parentNode.classList.add('rv-on')</script><?php endif; ?>
    <?php foreach ($galleries as $i => $g): ?>
        <?= Module::render('components/collection-card', ['g' => $g, 'n' => $i + 1, 'fx' => $c['reveal'], 'titles' => $c['titles'], 'style' => $c['style'], 'topts' => $c['style_opts']]) ?>
    <?php endforeach; ?>
    <?php if (!$galleries): ?><p class="coll-empty">Aún no hay galerías publicadas.</p><?php endif; ?>
</section>
