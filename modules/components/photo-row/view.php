<?php use App\Models\Photo; ?>
<article class="pr" data-id="<?= (int) $p['id'] ?>">
    <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="" loading="lazy">
    <div class="pr-fields">
        <div class="pr-line"><input type="text" class="t" value="<?= e($p['title']) ?>" aria-label="Nombre">
            <?php if ($aiConfigured): ?><button type="button" class="ad-btn is-ghost is-small" data-ai="title" title="Generar un nombre con IA">✨ Nombre</button><?php endif; ?></div>
        <div class="pr-line"><textarea class="d" rows="2" placeholder="Descripción" aria-label="Descripción"><?= e($p['caption']) ?></textarea>
            <?php if ($aiConfigured): ?><button type="button" class="ad-btn is-ghost is-small" data-ai="description" title="Generar una descripción con IA">✨ Descripción</button><?php endif; ?></div>
        <div class="pr-actions">
            <button type="button" class="ad-btn is-small" data-act="save">Guardar</button>
            <button type="button" class="ad-btn is-ghost is-small" data-act="cover"><?= $isCover ? '★ Portada' : '☆ Portada' ?></button>
            <button type="button" class="ad-btn is-danger is-small" data-act="delete">Borrar</button>
            <span class="pr-meta"><?= (int) $p['width'] ?>×<?= (int) $p['height'] ?></span>
        </div>
    </div>
</article>
