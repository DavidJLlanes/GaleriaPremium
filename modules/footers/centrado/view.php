<?php use App\Core\Module; ?>
<div class="fc <?= ($opts['line'] ?? '1') === '1' ? 'fc--line' : '' ?>">
    <p class="fc-name ftr-i"><?= e($f['name']) ?></p>
    <?php if ($f['tag'] !== ''): ?><p class="fc-tag ftr-i"><?= e($f['tag']) ?></p><?php endif; ?>
    <?= Module::render('components/footer-links', ['f' => $f]) ?>
    <p class="fc-copy ftr-i"><?= e($f['copy']) ?></p>
    <?= Module::render('components/footer-sig', ['f' => $f]) ?>
</div>
