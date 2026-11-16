<?php /* Va en el <head>: precarga de las fuentes críticas + @font-face + variables. $defaults = true en el panel de admin. */
$t = App\Services\Fonts::build(!empty($defaults));
foreach ($t['preload'] as $url): ?>
<link rel="preload" href="<?= e($url) ?>" as="font" type="font/woff2" crossorigin>
<?php endforeach; ?>
<style id="fonts"><?= $t['css'] ?></style>
