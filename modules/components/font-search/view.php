<?php /* Una sola vez por página: @font-face de todo el catálogo (solo el panel). El navegador descarga cada archivo únicamente cuando una fuente se muestra. */
static $done = false; if ($done) return; $done = true; ?>
<style id="fsFaces"><?php foreach (App\Services\Fonts::catalog() as $fid => $_) echo App\Services\Fonts::face($fid); ?></style>
