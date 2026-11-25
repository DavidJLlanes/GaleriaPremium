<?php use App\Core\Module; use App\Models\Photo; Module::use('components/reveal');
$o = $options ?? []; $n = count($photos);
$cap = in_array($o['captions'] ?? 'hover', ['hover', 'always', 'none'], true) ? $o['captions'] : 'hover'; ?>
<section class="gb gb--<?= e($o['pattern'] ?? 'bento') ?> gb--g<?= e($o['gap'] ?? 'm') ?> gb--c-<?= $cap ?>">
    <div class="gb-grid" data-lightbox>
        <?php foreach ($photos as $i => $p): ?>
            <a class="gb-item reveal" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" data-desc="<?= e($p['description'] ?? '') ?>"
               data-w="<?= (int) $p['width'] ?>" data-h="<?= (int) $p['height'] ?>" style="--d:<?= ($i % 4) * 80 ?>ms">
                <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="<?= e($p['title']) ?>" <?= $i > 5 ? 'loading="lazy"' : '' ?> draggable="false">
                <?= Module::render('components/photo-cats', ['cats' => $p['cats'] ?? []]) ?>
                <?php if ($cap !== 'none'): ?><span class="gb-cap"><small><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></small><?= e($p['title']) ?></span><?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>
