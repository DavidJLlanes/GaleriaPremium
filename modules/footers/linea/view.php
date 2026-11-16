<?php use App\Core\Module; ?>
<div class="fl">
    <p class="fl-c ftr-i"><span><?= e($f['copy']) ?></span><?php if ($f['tag'] !== ''): ?><span class="fl-tag"><?= e($f['tag']) ?></span><?php endif; ?></p>
    <?php if ($f['links']): ?><div class="ftr-i"><?= Module::render('components/social-icons', ['items' => $f['links']]) ?></div><?php endif; ?>
    <?= Module::render('components/footer-menu', ['f' => $f]) ?>
    <?= Module::render('components/footer-sig', ['f' => $f]) ?>
    <?php if ($f['top']) echo Module::render('components/footer-top'); ?>
</div>
