<?php use App\Services\Csrf; ?>
<h1>Inteligencia artificial</h1>
<p class="pnl-sub">Inteligencia artificial para nombres y descripciones de las fotos.</p>

<form class="pnl-card" method="post" action="/admin/ajustes" id="aiForm">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <h2>Proveedor de IA</h2>
    <div class="pnl-row">
        <div><label for="prov">Proveedor</label>
            <select id="prov" name="ai_provider">
                <?php foreach ($providers as $id => $pv): ?><option value="<?= e($id) ?>" <?= $ai['provider'] === $id ? 'selected' : '' ?>><?= e($pv['label']) ?></option><?php endforeach; ?>
            </select></div>
    </div>

    <?php foreach ($providers as $id => $pv): ?>
        <div class="as-prov" data-prov="<?= e($id) ?>">
            <div class="pnl-row">
                <?php if ($pv['needs_base_url']): ?>
                    <div><label>URL base de la API</label><input type="url" name="ai_base_url_<?= e($id) ?>" value="<?= e($pv['base_url']) ?>" placeholder="https://api.groq.com/openai/v1"></div>
                <?php endif; ?>
                <div><label>API key <?= $pv['needs_key'] ? '' : '(opcional)' ?></label>
                    <input type="password" name="ai_key_<?= e($id) ?>" autocomplete="off" placeholder="<?= $pv['key_set'] ? '•••••••• (guardada; déjala vacía para conservarla)' : 'Pega aquí tu API key' ?>">
                    <?php if ($pv['key_url']): ?><p class="pnl-hint">Consíguela en <a class="pnl-link" href="<?= e($pv['key_url']) ?>" target="_blank" rel="noopener"><?= e(parse_url($pv['key_url'], PHP_URL_HOST)) ?></a></p><?php endif; ?>
                    <?php if ($pv['key_set']): ?><label class="pnl-check" style="margin-top:.4rem"><input type="checkbox" name="ai_clear_<?= e($id) ?>"><span>Borrar la clave guardada</span></label><?php endif; ?></div>
                <div><label>Modelo (con visión)</label>
                    <div class="as-modelrow">
                        <input type="text" name="ai_model_<?= e($id) ?>" value="<?= e($pv['model']) ?>" placeholder="<?= e($pv['default_model']) ?>">
                        <button type="button" class="pnl-btn is-ghost is-small as-load">Ver modelos</button>
                    </div>
                    <p class="pnl-hint">Debe aceptar imágenes. Pulsa «Ver modelos» para elegir de la lista del proveedor.</p></div>
            </div>
            <div class="as-models" hidden>
                <div class="as-mhead">
                    <input type="text" class="as-mq" placeholder="Buscar modelo…" aria-label="Buscar modelo">
                    <label class="pnl-check as-vis"><input type="checkbox" class="as-mvis" checked><span>Solo con visión</span></label>
                    <span class="pnl-hint as-mcount"></span>
                </div>
                <div class="as-mlist"></div>
            </div>
        </div>
    <?php endforeach; ?>

    <h2 style="margin-top:1.6rem">Estilo de los textos</h2>
    <div class="pnl-row">
        <div><label>Idioma</label><input type="text" name="ai_language" value="<?= e($ai['language']) ?>" placeholder="español"></div>
    </div>
    <div style="margin-bottom:1rem"><label>Estilo de los NOMBRES (títulos)</label>
        <textarea name="ai_title_style" rows="2" placeholder="<?= e($defaults['title']) ?>"><?= e($ai['title_style']) ?></textarea>
        <p class="pnl-hint">Ejemplos: «Poético y breve, 2–4 palabras» · «Técnico y neutro» · «Misterioso, estilo cine negro» · «Divertido, con un juego de palabras».</p></div>
    <div style="margin-bottom:1rem"><label>Estilo de las DESCRIPCIONES</label>
        <textarea name="ai_desc_style" rows="2" placeholder="<?= e($defaults['desc']) ?>"><?= e($ai['desc_style']) ?></textarea></div>
    <label class="pnl-check" style="margin-bottom:1.4rem"><input type="checkbox" name="ai_auto" <?= $ai['auto'] ? 'checked' : '' ?>><span>Marcar «Generar con IA» por defecto al subir fotos</span></label>

    <div class="as-actions">
        <button class="pnl-btn">Guardar ajustes</button>
        <button type="button" class="pnl-btn is-ghost" id="aiTest">Probar conexión</button>
        <span class="pnl-hint" id="aiTestOut"></span>
    </div>
    <p class="pnl-hint" style="margin-top:.8rem">«Probar conexión» usa la configuración guardada: guarda primero los cambios.</p>
</form>
