<?php if (!empty($f['sig'])): $s = $f['sig']; $c = 'ftr-sig ftr-sig--' . e($s['style']) . ' ftr-i'; ?>
    <?php if ($s['url'] !== ''): ?><a class="<?= $c ?>" href="<?= e($s['url']) ?>" target="_blank" rel="noopener"><?= e($s['text']) ?></a>
    <?php else: ?><span class="<?= $c ?>"><?= e($s['text']) ?></span><?php endif; ?>
<?php endif; ?>
