<?php use App\Core\Module; ?>
<div class="fl">
    <div class="fl-l ftr-i"><span><?= e($f['copy']) ?></span><?php if ($f['tag'] !== ''): ?><span class="fl-tag"><?= e($f['tag']) ?></span><?php endif; ?></div>
    <?= Module::render('components/footer-links', ['f' => $f]) ?>
    <?= Module::render('components/footer-sig', ['f' => $f]) ?>
</div>
