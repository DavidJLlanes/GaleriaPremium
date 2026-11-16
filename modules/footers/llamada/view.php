<?php use App\Core\Module; $cta = trim((string) ($opts['cta'] ?? '')) ?: '¿Hablamos?'; ?>
<div class="fa">
    <?php $tag = $f['mail'] !== '' ? 'a' : 'p'; ?>
    <<?= $tag ?> class="fa-cta ftr-i" <?= $f['mail'] !== '' ? 'href="mailto:' . e($f['mail']) . '" data-magnet' : '' ?>><span><?= e($cta) ?></span><?= $f['mail'] !== '' ? '<i aria-hidden="true">↗</i>' : '' ?></<?= $tag ?>>
    <?php if ($f['tag'] !== ''): ?><p class="fa-tag ftr-i"><?= e($f['tag']) ?></p><?php endif; ?>
    <div class="fa-bar">
        <span class="ftr-i"><?= e($f['copy']) ?></span>
        <?= Module::render('components/footer-links', ['f' => $f]) ?>
        <?= Module::render('components/footer-sig', ['f' => $f]) ?>
    </div>
</div>
