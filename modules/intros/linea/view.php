<div class="inn <?= ($opts['glow'] ?? '1') === '1' ? 'inn--glow' : '' ?>">
    <h2 class="inn-title"><?= e($text) ?></h2>
    <i class="inn-line" aria-hidden="true"></i>
    <?php if ($sub !== ''): ?><p class="inn-sub"><?= e($sub) ?></p><?php endif; ?>
</div>
