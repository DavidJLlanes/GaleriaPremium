<?php /* Opciones propias de cada diseño (module.php → 'options'). Solo se ven las del diseño elegido. Datos: $styles, $gallery. */
use App\Models\Gallery;
$saved = json_decode((string) ($gallery['options'] ?? '{}'), true) ?: [];
foreach ($styles as $sid => $m): if (empty($m['options'])) continue; ?>
    <fieldset class="so" data-style="<?= e($sid) ?>" hidden>
        <legend>Opciones de «<?= e($m['label']) ?>»</legend>
        <?php foreach ($m['options'] as $k => $o): $v = $saved[$sid][$k] ?? $o['default']; $name = "opt[$sid][$k]"; ?>
            <?php if ($o['type'] === 'bool'): ?>
                <label class="pnl-check so-row"><input type="checkbox" name="<?= e($name) ?>" <?= $v ? 'checked' : '' ?>><span><?= e($o['label']) ?></span></label>
            <?php elseif ($o['type'] === 'number'): ?>
                <div class="so-row"><label><?= e($o['label']) ?></label>
                    <input type="number" name="<?= e($name) ?>" value="<?= (int) $v ?>" min="<?= (int) $o['min'] ?>" max="<?= (int) $o['max'] ?>" style="max-width:140px"></div>
            <?php elseif ($o['type'] === 'enum'): ?>
                <div class="so-row"><label><?= e($o['label']) ?></label>
                    <select name="<?= e($name) ?>" style="max-width:220px"><?php foreach ($o['choices'] as $cv => $cl): ?><option value="<?= e($cv) ?>" <?= (string) $v === (string) $cv ? 'selected' : '' ?>><?= e($cl) ?></option><?php endforeach; ?></select></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </fieldset>
<?php endforeach; ?>
