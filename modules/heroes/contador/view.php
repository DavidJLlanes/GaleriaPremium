<?php use App\Core\Module; use App\Models\Photo;
$list = array_values(array_filter($galleries ?? [], fn ($g) => !empty($g['cover']))); $n = count($list);
$pad = static fn (int $i) => str_pad((string) $i, 2, '0', STR_PAD_LEFT); ?>
<section class="hx-count" data-hero data-interval="<?= (int) ($interval ?? 6500) ?>">
<?php if (!$n): echo Module::render('components/hero-shell', ['class' => 'hx-count-empty', 'media' => '', 'copy' => $copy ?? []]); else: ?>
    <div class="hxn-bgs">
        <?php foreach ($list as $i => $g): ?><img class="<?= $i === 0 ? 'is-active' : '' ?>" src="<?= e(Photo::url($g['cover'])) ?>" alt=""<?= $i === 0 ? ' fetchpriority="high"' : ' loading="lazy"' ?>><?php endforeach; ?>
    </div>
    <div class="hxn-shade"></div>
    <p class="hxn-eyebrow"><?= e($copy['eyebrow'] ?? '') ?></p>
    <div class="hxn-slides">
        <?php foreach ($list as $i => $g): ?>
            <a class="hxn-slide<?= $i === 0 ? ' is-active' : '' ?>" href="/galeria/<?= e($g['slug']) ?>" tabindex="<?= $i === 0 ? 0 : -1 ?>">
                <span class="hxn-num"><b><?= $pad($i + 1) ?></b><i></i><em><?= $pad($n) ?></em></span>
                <h2><?= e($g['title']) ?></h2>
                <span class="hxn-meta"><?= (int) $g['photo_count'] ?> fotos · Ver galería →</span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php if ($n > 1): ?>
        <div class="hxn-nav"><button type="button" class="hxn-prev" aria-label="Galería anterior">←</button><button type="button" class="hxn-next" aria-label="Galería siguiente">→</button></div>
        <ol class="hxn-list">
            <?php foreach ($list as $i => $g): ?><li><button type="button" class="<?= $i === 0 ? 'is-active' : '' ?>" data-i="<?= $i ?>"><?= $pad($i + 1) ?> <span><?= e($g['title']) ?></span></button></li><?php endforeach; ?>
        </ol>
    <?php endif; ?>
    <a class="hxn-down" href="#galerias"><?= e($copy['cta'] ?? 'Explorar galerías') ?> ↓</a>
<?php endif; ?>
</section>
