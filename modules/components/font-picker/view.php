<?php use App\Services\Fonts;
$cat = Fonts::catalog();
$faces = []; foreach ($cat as $id => $_) $faces[$id] = Fonts::face($id);
$groups = []; foreach ($cat as $id => $f) $groups[$f['cat']][$id] = $f['label'];
?>
<section class="ad-card fp" data-faces="<?= e(json_encode($faces, JSON_UNESCAPED_SLASHES)) ?>" data-fam="<?= e(json_encode(array_map(fn ($id) => Fonts::family($id), array_combine(array_keys($cat), array_keys($cat))), JSON_UNESCAPED_UNICODE)) ?>">
    <h2>Tipografías</h2>
    <p class="ad-hint" style="margin:-.6rem 0 1.2rem">Una tipografía para cada tipo de texto. Están alojadas en tu servidor (rápidas y sin enviar datos a Google). La vista previa se actualiza al elegir.</p>
    <?php foreach (Fonts::ROLES as $role => [$label, $desc, $def, $sample]):
        $cur = Fonts::of($role); ?>
        <div class="fp-row">
            <div class="fp-ctl">
                <label for="fp_<?= e($role) ?>"><?= e($label) ?></label>
                <select id="fp_<?= e($role) ?>" name="site_font_<?= e($role) ?>">
                    <option value="" <?= \App\Services\Site::get("font_$role") === '' ? 'selected' : '' ?>>Por defecto (<?= e($cat[$def]['label']) ?>)</option>
                    <?php foreach ($groups as $g => $items): ?>
                        <optgroup label="<?= e(Fonts::CATS[$g] ?? $g) ?>">
                            <?php foreach ($items as $id => $name): ?><option value="<?= e($id) ?>" <?= \App\Services\Site::get("font_$role") === $id ? 'selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <p class="ad-hint"><?= e($desc) ?></p>
            </div>
            <div class="fp-prev" data-def="<?= e($def) ?>" data-cur="<?= e($cur) ?>" style="font-family:<?= e(Fonts::family($cur)) ?>"><?= e($sample) ?></div>
        </div>
    <?php endforeach; ?>
</section>
