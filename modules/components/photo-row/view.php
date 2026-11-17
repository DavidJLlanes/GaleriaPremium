<?php use App\Models\Photo; use App\Services\Crop;
$crop = !empty($canCrop) ? Crop::of($p) : null; ?>
<article class="pr" id="foto-<?= (int) $p['id'] ?>" data-id="<?= (int) $p['id'] ?>" data-src="<?= e(Photo::url($p['filename'])) ?>" data-iw="<?= (int) $p['width'] ?>" data-ih="<?= (int) $p['height'] ?>" data-crop="<?= $crop ? e(json_encode($crop)) : '' ?>">
    <span class="bk-box" role="checkbox" aria-checked="false" tabindex="0" title="Seleccionar">✓</span>
    <span class="srt-handle" title="Arrastra para reordenar" aria-label="Reordenar">⠿</span>
    <?php if ($crop): ?>
        <div class="pr-th" style="aspect-ratio:<?= round(Crop::ratio($crop, max(1, (int) $p['width']), max(1, (int) $p['height'])), 4) ?>">
            <img src="<?= e(Photo::url($p['filename'])) ?>" alt="" loading="lazy" style="width:<?= round(100 / $crop['w'], 3) ?>%;left:-<?= round($crop['x'] / $crop['w'] * 100, 3) ?>%;top:-<?= round($crop['y'] / $crop['h'] * 100, 3) ?>%"></div>
    <?php else: ?><img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="" loading="lazy"><?php endif; ?>
    <div class="pr-fields">
        <div class="pr-line"><input type="text" class="t" value="<?= e($p['title']) ?>" aria-label="Nombre">
            <?php if ($aiConfigured): ?><button type="button" class="pnl-btn is-ghost is-small" data-ai="title" title="Generar un nombre con IA">✨ Nombre</button><?php endif; ?></div>
        <div class="pr-line"><textarea class="d" rows="2" placeholder="Descripción" aria-label="Descripción"><?= e($p['caption']) ?></textarea>
            <?php if ($aiConfigured): ?><button type="button" class="pnl-btn is-ghost is-small" data-ai="description" title="Generar una descripción con IA">✨ Descripción</button><?php endif; ?></div>
        <div class="pr-line pr-cats"><?= \App\Core\Module::render('components/cat-picker', ['selected' => $p['cats'] ?? []]) ?></div>
        <div class="pr-actions">
            <button type="button" class="pnl-btn is-small" data-act="save">Guardar</button>
            <?php if (!empty($canCrop)): ?><button type="button" class="pnl-btn is-ghost is-small" data-crop-open><?= !empty($isFirst) ? '✂ Recortar · fija la proporción' : '✂ Recortar' ?></button><?php endif; ?>
            <button type="button" class="pnl-btn is-ghost is-small" data-act="cover"><?= $isCover ? '★ Portada' : '☆ Portada' ?></button>
            <button type="button" class="pnl-btn is-danger is-small" data-act="delete">Borrar</button>
            <span class="pr-meta"><?= (int) $p['width'] ?>×<?= (int) $p['height'] ?></span>
        </div>
    </div>
</article>
