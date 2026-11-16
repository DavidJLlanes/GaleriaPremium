<div class="inc inc--<?= e($opts['open'] ?? 'h') ?>">
    <i class="inc-p inc-a"></i><i class="inc-p inc-b"></i>
    <div class="inc-t">
        <h2 class="inc-title"><?= e($text) ?></h2>
        <?php if ($sub !== ''): ?><p class="inc-sub"><?= e($sub) ?></p><?php endif; ?>
    </div>
</div>
