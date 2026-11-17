<?php /* Selector de categorías (admin y subida global). Params: [$selected = nombres ya elegidos], [$label]. Lee su valor con el.closest('[data-cp]')._cp.get().
 * La lista de categorías guardadas se emite una sola vez por página (window.CatAll). */
use App\Services\Categories;
if (empty($GLOBALS['__cat_all_sent'])): $GLOBALS['__cat_all_sent'] = true; ?><script>window.CatAll=<?= json_encode(Categories::names(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;window.CatMax=<?= Categories::MAX ?>;</script><?php endif; ?>
<div class="cp" data-cp data-value="<?= e(json_encode(array_values($selected ?? []), JSON_UNESCAPED_UNICODE)) ?>">
    <?php if (!empty($label)): ?><span class="cp-label"><?= e($label) ?></span><?php endif; ?>
    <div class="cp-box"><span class="cp-chips"></span><input class="cp-in" type="text" maxlength="<?= Categories::LEN ?>" placeholder="Categorías (máx. <?= Categories::MAX ?>)…" autocomplete="off" role="combobox" aria-expanded="false" aria-label="Categorías"></div>
    <ul class="cp-list" role="listbox" hidden></ul>
</div>
