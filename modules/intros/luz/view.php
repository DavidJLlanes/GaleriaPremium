<?php use App\Core\Module; Module::use('components/stroke-order'); Module::use('components/light-pen'); ?>
<div class="inl" data-speed="<?= (int) ($opts['speed'] ?? 6) ?>">
    <h2 class="inl-title"><?= e($text) ?></h2>
    <canvas class="inl-canvas" aria-hidden="true"></canvas>
    <?php if ($sub !== ''): ?><p class="inl-sub"><?= e($sub) ?></p><?php endif; ?>
</div>
