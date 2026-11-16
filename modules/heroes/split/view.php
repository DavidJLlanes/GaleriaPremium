<?php use App\Core\Module; use App\Models\Photo;
$duo = ($opts['layout'] ?? 'text') === 'duo' && count($photos) > 1;
$copyHtml = '<div class="hxsp-copy"><p class="hxsp-eyebrow">' . e($copy['eyebrow'] ?? '') . '</p><h1>' . e($copy['title'] ?? '') . '</h1>'
          . Module::render('components/button', ['href' => '#galerias', 'label' => $copy['cta'] ?? 'Explorar galerías']) . '</div>';
?>
<section class="hx-split <?= $duo ? 'is-duo' : 'is-text' ?>" data-hero data-interval="<?= (int) ($interval ?? 6500) ?>">
<?php if ($duo): ?>
    <?php foreach (array_slice($photos, 0, 2) as $i => $p): ?>
        <div class="hxsp-pane" tabindex="0"><img src="<?= e(Photo::url($p['filename'])) ?>" alt="<?= e($p['title']) ?>"<?= $i ? '' : ' fetchpriority="high"' ?>><span class="hxsp-cap"><?= e($p['title']) ?></span></div>
    <?php endforeach; ?>
    <?= $copyHtml ?>
<?php else: ?>
    <?= $copyHtml ?>
    <div class="hxsp-photo">
        <?php foreach ($photos as $i => $p): ?><img class="<?= $i === 0 ? 'is-active' : '' ?>" src="<?= e(Photo::url($p['filename'])) ?>" alt="<?= e($p['title']) ?>"<?= $i === 0 ? ' fetchpriority="high"' : ' loading="lazy"' ?>><?php endforeach; ?>
    </div>
<?php endif; ?>
    <?= Module::render('components/scroll-hint') ?>
</section>
