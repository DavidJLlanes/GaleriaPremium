<?php /* Va en el <head>: precarga de las fuentes críticas + @font-face + variables. $defaults = true en el panel de admin. */
$t = App\Services\Fonts::build(!empty($defaults));
foreach ($t['preload'] as $id): ?>
<link rel="preload" href="<?= e(App\Services\Fonts::url($id, App\Services\Fonts::preloadFile($id))) ?>" as="font" type="font/woff2" crossorigin>
<?php endforeach; ?>
<style id="fonts"><?= $t['css'] ?></style>
