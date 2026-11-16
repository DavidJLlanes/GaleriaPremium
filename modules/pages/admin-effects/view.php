<?php use App\Core\Module; use App\Services\{Csrf, Effects}; ?>
<h1>Efectos</h1>
<p class="ad-sub">Detalles que dan sensación premium a toda la web. Cada uno se puede activar o quitar; los cambios se ven al guardar y recargar la web.</p>
<form method="post" action="/admin/efectos">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <?php $groups = ['Movimiento' => ['smooth', 'transition'], 'Cursor' => ['cursor', 'cursor_label'], 'Imagen y ambiente' => ['lqip', 'grain', 'sound']];
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
