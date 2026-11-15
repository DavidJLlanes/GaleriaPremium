<?php /* Editor de recorte (modal). Se abre con window.Cropper.open({...}). Sin datos propios. */ ?>
<div class="cr" id="cropper" hidden role="dialog" aria-modal="true" aria-label="Recortar foto">
    <div class="cr-box">
        <div class="cr-head"><b class="cr-title">Recortar</b><button type="button" class="cr-x" aria-label="Cerrar">×</button></div>
        <p class="cr-hint"></p>
        <div class="cr-presets" hidden>
            <?php foreach (['1:1' => 1, '4:3' => 4 / 3, '3:2' => 3 / 2, '16:9' => 16 / 9, '2:1' => 2, '4:5' => .8, '3:4' => .75, '2:3' => 2 / 3] as $l => $r): ?>
                <button type="button" data-r="<?= round($r, 5) ?>"><?= e($l) ?></button>
            <?php endforeach; ?>
            <button type="button" data-r="free" class="is-on">Libre</button>
        </div>
        <div class="cr-stage">
            <img class="cr-img" alt="" draggable="false">
            <div class="cr-rect"><i data-h="nw"></i><i data-h="ne"></i><i data-h="sw"></i><i data-h="se"></i></div>
        </div>
        <div class="cr-foot">
            <label class="cr-zoom">Zoom <input type="range" min="10" max="100" value="100"></label>
            <span class="cr-info"></span>
            <button type="button" class="ad-btn is-ghost cr-cancel">Cancelar</button>
            <button type="button" class="ad-btn cr-save">Guardar recorte</button>
        </div>
    </div>
</div>
