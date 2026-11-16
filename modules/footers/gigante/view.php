<?php use App\Core\Module; ?>
<div class="fg <?= ($opts['outline'] ?? '0') === '1' ? 'fg--outline' : '' ?>">
    <p class="fg-word ftr-i" aria-label="<?= e($f['name']) ?>"><span aria-hidden="true"><?= e($f['name']) ?></span></p>
    <?php if ($f['tag'] !== ''): ?><p class="fg-tag ftr-i"><?= e($f['tag']) ?></p><?php endif; ?>
    <div class="fg-bar">
        <span class="ftr-i"><?= e($f['copy']) ?></span>
        <?= Module::render('components/footer-links', ['f' => $f]) ?>
        <?= Module::render('components/footer-sig', ['f' => $f]) ?>
    </div>
</div>
