<?php /* Campos de los ajustes de un módulo. Datos: $spec (options del module.php), $values (valores actuales), $name (prefijo del campo, p. ej. title_opt[luz]). */ ?>
<div class="ad-row">
    <?php foreach ($spec as $k => $o): $v = (string) ($values[$k] ?? ($o['default'] ?? '')); $nm = $name . '[' . $k . ']'; ?>
        <div>
            <?php if ($o['type'] === 'bool'): ?>
                <label class="ad-check"><input type="checkbox" name="<?= e($nm) ?>" value="1" <?= $v === '1' ? 'checked' : '' ?>><span><?= e($o['label']) ?></span></label>
            <?php else: ?>
                <label><?= e($o['label']) ?></label>
                <?php if ($o['type'] === 'color'): ?><input type="color" name="<?= e($nm) ?>" value="<?= e($v) ?>">
                <?php elseif ($o['type'] === 'range'): ?><input type="range" name="<?= e($nm) ?>" min="<?= e($o['min']) ?>" max="<?= e($o['max']) ?>" step="<?= e($o['step'] ?? 1) ?>" value="<?= e($v) ?>" oninput="this.nextElementSibling.textContent=this.value"><output><?= e($v) ?></output>
                <?php elseif ($o['type'] === 'select'): ?><select name="<?= e($nm) ?>"><?php foreach ($o['choices'] as $cv => $cl): ?><option value="<?= e($cv) ?>" <?= (string) $cv === $v ? 'selected' : '' ?>><?= e($cl) ?></option><?php endforeach; ?></select>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($o['hint'])): ?><p class="ad-hint"><?= e($o['hint']) ?></p><?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
