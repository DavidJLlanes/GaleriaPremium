<?php use App\Services\Csrf; ?>
<h1>Ajustes</h1>
<p class="ad-sub">Inteligencia artificial para nombres y descripciones de las fotos.</p>

<form class="ad-card" method="post" action="/admin/ajustes" id="aiForm">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <h2>Proveedor de IA</h2>
    <div class="ad-row">
        <div><label for="prov">Proveedor</label>
            <select id="prov" name="ai_provider">
                <?php foreach ($providers as $id => $pv): ?><option value="<?= e($id) ?>" <?= $ai['provider'] === $id ? 'selected' : '' ?>><?= e($pv['label']) ?></option><?php endforeach; ?>
            </select></div>
    </div>

    <?php foreach ($providers as $id => $pv): ?>
        <div class="as-prov" data-prov="<?= e($id) ?>">
            <div class="ad-row">
                <?php if ($pv['needs_base_url']): ?>
                    <div><label>URL base de la API</label><input type="url" name="ai_base_url_<?= e($id) ?>" value="<?= e($pv['base_url']) ?>" placeholder="https://api.groq.com/openai/v1"></div>
                <?php endif; ?>
                <div><label>API key <?= $pv['needs_key'] ? '' : '(opcional)' ?></label>
                    <input type="password" name="ai_key_<?= e($id) ?>" autocomplete="off" placeholder="<?= $pv['key_set'] ? '•••••••• (guardada; déjala vacía para conservarla)' : 'Pega aquí tu API key' ?>">
                    <?php if ($pv['key_url']): ?><p class="ad-hint">Consíguela en <a class="ad-link" href="<?= e($pv['key_url']) ?>" target="_blank" rel="noopener"><?= e(parse_url($pv['key_url'], PHP_URL_HOST)) ?></a></p><?php endif; ?>
                    <?php if ($pv['key_set']): ?><label class="ad-check" style="margin-top:.4rem"><input type="checkbox" name="ai_clear_<?= e($id) ?>"> Borrar la clave guardada</label><?php endif; ?></div>
                <div><label>Modelo (con visión)</label>
                    <div class="as-modelrow">
                        <input type="text" name="ai_model_<?= e($id) ?>" value="<?= e($pv['model']) ?>" placeholder="<?= e($pv['default_model']) ?>">
                        <button type="button" class="ad-btn is-ghost is-small as-load">Ver modelos</button>
                    </div>
                    <p class="ad-hint">Debe aceptar imágenes. Pulsa «Ver modelos» para elegir de la lista del proveedor.</p></div>
            </div>
            <div class="as-models" hidden>
                <div class="as-mhead">
                    <input type="text" class="as-mq" placeholder="Buscar modelo…" aria-label="Buscar modelo">
                    <label class="ad-check as-vis"><input type="checkbox" class="as-mvis" checked> Solo con visión</label>
                    <span class="ad-hint as-mcount"></span>
                </div>
                <div class="as-mlist"></div>
            </div>
        </div>
    <?php endforeach; ?>

    <h2 style="margin-top:1.6rem">Estilo de los textos</h2>
    <div class="ad-row">
        <div><label>Idioma</label><input type="text" name="ai_language" value="<?= e($ai['language']) ?>" placeholder="español"></div>
    </div>
    <div style="margin-bottom:1rem"><label>Estilo de los NOMBRES (títulos)</label>
        <textarea name="ai_title_style" rows="2" placeholder="<?= e($defaults['title']) ?>"><?= e($ai['title_style']) ?></textarea>
        <p class="ad-hint">Ejemplos: «Poético y breve, 2–4 palabras» · «Técnico y neutro» · «Misterioso, estilo cine negro» · «Divertido, con un juego de palabras».</p></div>
    <div style="margin-bottom:1rem"><label>Estilo de las DESCRIPCIONES</label>
        <textarea name="ai_desc_style" rows="2" placeholder="<?= e($defaults['desc']) ?>"><?= e($ai['desc_style']) ?></textarea></div>
    <label class="ad-check" style="margin-bottom:1.4rem"><input type="checkbox" name="ai_auto" <?= $ai['auto'] ? 'checked' : '' ?>> Marcar «Generar con IA» por defecto al subir fotos</label>

    <div class="as-actions">
        <button class="ad-btn">Guardar ajustes</button>
        <button type="button" class="ad-btn is-ghost" id="aiTest">Probar conexión</button>
        <span class="ad-hint" id="aiTestOut"></span>
    </div>
    <p class="ad-hint" style="margin-top:.8rem">«Probar conexión» usa la configuración guardada: guarda primero los cambios.</p>
</form>
