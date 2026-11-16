<div class="inr inr--<?= e($opts['dir'] ?? 'up') ?>">
    <h2 class="inr-title" aria-label="<?= e($text) ?>"><?php $i = 0; foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) as $ch): ?><span class="inr-w" aria-hidden="true"><span class="inr-c" style="--i:<?= $i++ ?>"><?= $ch === ' ' ? '&nbsp;' : e($ch) ?></span></span><?php endforeach; ?></h2>
    <?php if ($sub !== ''): ?><p class="inr-sub"><?= e($sub) ?></p><?php endif; ?>
</div>
