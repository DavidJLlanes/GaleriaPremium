<?php use App\Services\{Fonts, Site};
$cat = Fonts::catalog();
$meta = []; foreach ($cat as $id => $_) $meta[$id] = Fonts::meta($id);
$groups = []; foreach ($cat as $id => $f) $groups[$f['cat']][$id] = $f['label'];
$customs = array_filter($cat, fn ($f) => !empty($f['custom']));
$opt = static function (array $groups, string $sel): string {
    $h = '';
    foreach (Fonts::CATS as $g => $name) {
        if (empty($groups[$g])) continue;
        $h .= '<optgroup label="' . e($name) . '" data-cat="' . e($g) . '">';
        foreach ($groups[$g] as $id => $label) $h .= '<option value="' . e($id) . '"' . ($sel === $id ? ' selected' : '') . '>' . e($label) . '</option>';
        $h .= '</optgroup>';
    }
    return $h;
};
?>
<section class="ad-card fp">
    <h2>Tipografías</h2>
    <p class="ad-hint" style="margin:-.6rem 0 1.2rem">Una tipografía, un grosor y (si la fuente lo permite) cursiva para cada tipo de texto. Todo está alojado en tu servidor: rápido y sin enviar datos a Google. La vista previa se actualiza al elegir.</p>
    <script type="application/json" id="fpMeta"><?= json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
    <script type="application/json" id="fpWeights"><?= json_encode(Fonts::WEIGHTS, JSON_UNESCAPED_UNICODE) ?></script>
    <?php foreach (Fonts::ROLES as $role => [$label, $desc, $def, $sample]):
        $saved = Site::get("font_$role"); $cur = Fonts::of($role); $sw = Site::get("fontw_$role"); $si = Site::on("fonti_$role");
        $m = $meta[$cur]; ?>
        <div class="fp-row" data-def="<?= e($def) ?>">
            <div class="fp-ctl">
                <label for="fp_<?= e($role) ?>"><?= e($label) ?></label>
                <select id="fp_<?= e($role) ?>" name="site_font_<?= e($role) ?>" class="fp-font">
                    <option value="" <?= $saved === '' ? 'selected' : '' ?>><?= $role === 'herobtn' ? 'Igual que «Botones»' : 'Por defecto (' . e($cat[$def]['label']) . ')' ?></option>
                    <?= $opt($groups, $saved) ?>
                </select>
                <div class="fp-style">
                    <select name="site_fontw_<?= e($role) ?>" class="fp-weight" aria-label="Grosor">
                        <option value="">Grosor por defecto</option>
                        <?php foreach ($m['w'] as $w): ?><option value="<?= $w ?>" <?= (string) $w === $sw ? 'selected' : '' ?>><?= $w ?> · <?= e(Fonts::WEIGHTS[$w]) ?></option><?php endforeach; ?>
                    </select>
                    <label class="fp-it"><input type="checkbox" name="site_fonti_<?= e($role) ?>" value="1" class="fp-italic" <?= $si ? 'checked' : '' ?> <?= $m['i'] ? '' : 'disabled' ?>> <span>Cursiva</span></label>
                </div>
                <p class="ad-hint"><?= e($desc) ?></p>
            </div>
            <div class="fp-prev" style="font-family:<?= e($m['fam']) ?>"><?= e($sample) ?></div>
        </div>
    <?php endforeach; ?>

    <div class="fp-up">
        <h3>Subir fuente propia</h3>
        <p class="ad-hint">Acepta .woff2, .woff, .ttf y .otf (máx. 4 MB). Se usan tal cual; .woff2 es el más ligero. Para añadir otro grosor o la cursiva de la misma familia, sube otro archivo con el mismo nombre.</p>
        <div class="fp-up-grid">
            <div><label for="fpuName">Nombre de la fuente</label><input type="text" id="fpuName" maxlength="60" placeholder="Mi fuente"></div>
            <div><label for="fpuWeight">Grosor del archivo</label>
                <select id="fpuWeight"><option value="var">Variable (100–900)</option>
                    <?php foreach (Fonts::WEIGHTS as $w => $n): ?><option value="<?= $w ?>" <?= $w === 400 ? 'selected' : '' ?>><?= $w ?> · <?= e($n) ?></option><?php endforeach; ?></select></div>
            <label class="fp-it"><input type="checkbox" id="fpuItalic"> <span>Es cursiva</span></label>
            <div class="fp-up-file"><input type="file" id="fpuFile" accept=".woff2,.woff,.ttf,.otf"></div>
            <button type="button" class="ad-btn" id="fpuBtn">Subir fuente</button>
        </div>
        <div class="fp-own" id="fpOwn" <?= $customs ? '' : 'hidden' ?>>
            <h3>Mis fuentes</h3>
            <ul id="fpOwnList">
                <?php foreach ($customs as $id => $f): ?>
                    <li data-id="<?= e($id) ?>"><b style="font-family:<?= e(Fonts::family($id)) ?>"><?= e($f['label']) ?></b>
                        <small><?= e(implode(' · ', \App\Controllers\Admin\FontController::variants($id))) ?></small>
                        <button type="button" class="ad-btn is-ghost is-small fp-del">Borrar</button></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
