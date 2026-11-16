<div class="inf <?= ($opts['blur'] ?? '1') === '1' ? 'inf--blur' : '' ?>">
    <h2 class="inf-title"><?= e($text) ?></h2>
    <?php if ($sub !== ''): ?><p class="inf-sub"><?= e($sub) ?></p><?php endif; ?>
</div>
