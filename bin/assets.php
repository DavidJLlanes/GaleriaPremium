<?php
declare(strict_types=1);
/**
 * Publica el CSS/JS de cada módulo en public/assets/m/<tipo>/<nombre>/.
 * Se ejecuta en cada despliegue (deploy/deploy.sh). En desarrollo no hace falta: public/router.php los sirve directo.
 */
require __DIR__ . '/../app/bootstrap.php';

$out = ROOT . '/public/assets/m';
if (is_dir($out)) { exec('rm -rf ' . escapeshellarg($out)); }

$n = 0;
foreach (glob(ROOT . '/modules/*/*', GLOB_ONLYDIR) ?: [] as $mod) {
    $id = basename(dirname($mod)) . '/' . basename($mod);
    foreach (['style.css', 'script.js'] as $f) {
        if (!is_file("$mod/$f")) continue;
        @mkdir("$out/$id", 0775, true);
        copy("$mod/$f", "$out/$id/$f");
        $n++;
    }
}
echo "✔ $n assets publicados\n";
