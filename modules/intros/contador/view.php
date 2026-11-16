<div class="ino ino--<?= e($opts['pos'] ?? 'corner') ?>">
    <h2 class="ino-title"><?= e($text) ?></h2>
    <?php if ($sub !== ''): ?><p class="ino-sub"><?= e($sub) ?></p><?php endif; ?>
    <div class="ino-num" aria-hidden="true"><span class="ino-n">0</span><small>%</small></div>
    <i class="ino-bar" aria-hidden="true"></i>
</div>
