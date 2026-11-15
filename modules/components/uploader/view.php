<section class="ad-card up" id="uploader" data-url="/admin/galeria/<?= (int) $galleryId ?>/subir">
    <h2>Subir fotos</h2>
    <label class="up-drop" id="upDrop">
        <input type="file" id="upInput" accept="image/jpeg,image/png,image/webp,image/gif" multiple hidden>
        <strong>Arrastra tus fotos aquí</strong><span>o pulsa para elegirlas · JPG, PNG, WebP · se convierten a WebP 75%</span>
    </label>

    <div class="up-queue" id="upQueue"></div>

    <div class="up-bar" id="upBar" hidden>
        <label class="ad-check">
            <input type="checkbox" id="upAi" <?= ($aiConfigured && $aiAuto) ? 'checked' : '' ?> <?= $aiConfigured ? '' : 'disabled' ?>>
            Generar con IA el nombre y la descripción que deje vacíos
        </label>
        <?php if (!$aiConfigured): ?><span class="ad-hint">La IA no está configurada. <a class="ad-link" href="/admin/ajustes">Configurarla</a></span><?php endif; ?>
        <div class="up-actions">
            <button type="button" class="ad-btn is-ghost" id="upClear">Vaciar</button>
            <button type="button" class="ad-btn" id="upGo">Subir <span id="upCount"></span></button>
        </div>
    </div>
</section>
