<?php use App\Core\Module; use App\Services\{Csrf, Effects}; ?>
<h1>Efectos</h1>
<p class="ad-sub">Detalles que dan sensación premium a toda la web. Cada uno se puede activar o quitar; los cambios se ven al guardar y recargar la web.</p>
<form method="post" action="/admin/efectos">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <section class="ad-card">
        <h2>Cursor propio (solo ordenador)</h2>
        <?php $off = ['off' => ['label' => 'Sin cursor propio', 'desc' => 'Se usa la flecha normal del sistema.', 'icon' => '<path d="M24 8l14 12-6 1.4 3.4 7-3.4 1.6-3.4-7L24 28z" fill="none" stroke="currentColor" stroke-width="1.8"/>']];
        echo Module::render('components/style-picker', ['styles' => $off + $cursors, 'name' => 'fx[cursor]', 'selected' => $v['cursor']]); ?>
        <div class="fx-cur-lbl"><?= Module::render('components/option-fields', ['spec' => array_intersect_key(Effects::SPEC, ['cursor_label' => 1]), 'values' => $v, 'name' => 'fx']) ?></div>
        <p class="ad-hint">Sobre las fotos, el cursor muestra esa etiqueta (donde el diseño la use) y los botones se acercan suavemente a él.</p>
    </section>
    <?php foreach ($cursors as $cid => $cm): if (empty($cm['options'])) continue; ?>
        <section class="ad-card" data-cur-opts="<?= e($cid) ?>" hidden>
            <h2>Ajustes del cursor «<?= e($cm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $cm['options'], 'values' => \App\Services\Effects::cursorOptions($cid), 'name' => "cursor_opt[$cid]"]) ?>
        </section>
    <?php endforeach; ?>
    <?php $groups = ['Movimiento' => ['smooth', 'transition'], 'Imagen y ambiente' => ['lqip', 'grain', 'sound']];
    foreach ($groups as $title => $keys): ?>
        <section class="ad-card">
            <h2><?= e($title) ?></h2>
            <?= Module::render('components/option-fields', ['spec' => array_intersect_key(Effects::SPEC, array_flip($keys)), 'values' => $v, 'name' => 'fx']) ?>
        </section>
    <?php endforeach; ?>
    <p class="ad-hint">La pantalla de entrada tiene su propio apartado: <a href="/admin/entrada">Entrada</a>.</p>
    <button class="ad-btn">Guardar efectos</button>
    <a class="ad-btn is-ghost" href="/" target="_blank" rel="noopener" style="margin-left:.5rem">Ver la web ↗</a>
</form>
