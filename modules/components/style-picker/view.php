<?php /* Selector visual de diseño de galería. Datos: $styles (Module::all('galleries')), $name (campo, por defecto "style"), $selected. */
$name = $name ?? 'style'; $selected = $selected ?? array_key_first($styles); ?>
<div class="sp" role="radiogroup">
    <?php foreach ($styles as $id => $m): ?>
        <label class="sp-opt">
            <input type="radio" name="<?= e($name) ?>" value="<?= e($id) ?>" <?= $selected === $id ? 'checked' : '' ?>>
            <span class="sp-card">
                <svg viewBox="0 0 64 40" fill="currentColor" stroke="none" aria-hidden="true"><?= $m['icon'] ?? '<rect x="4" y="4" width="56" height="32"/>' ?></svg>
                <b><?= e($m['label']) ?></b>
                <small><?= e($m['desc'] ?? '') ?></small>
            </span>
        </label>
    <?php endforeach; ?>
</div>
