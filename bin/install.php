<?php
declare(strict_types=1);
/**
 * Instalador (idempotente).
 *   php bin/install.php                 -> crea/actualiza el esquema
 *   php bin/install.php --demo          -> + datos demo (solo si no hay galerías)
 *   php bin/install.php --demo --reset  -> BORRA galerías, fotos y ajustes y recarga la demo
 */
require __DIR__ . '/../app/bootstrap.php';

use App\Core\{Config, Database};
use App\Models\Setting;
use App\Services\ImageProcessor;

$pdo = Database::pdo();
$pdo->exec(file_get_contents(ROOT . '/database/schema.sql'));
echo "✔ Esquema listo\n";

if (!in_array('--demo', $argv, true)) exit(0);

$dir = rtrim(Config::get('uploads.dir'), '/');
@mkdir("$dir/originals", 0775, true);
@mkdir("$dir/thumbs", 0775, true);

if (in_array('--reset', $argv, true)) {
    $pdo->exec('DELETE FROM photos; DELETE FROM galleries; DELETE FROM settings; DELETE FROM sqlite_sequence');
    foreach (['originals', 'thumbs'] as $d) foreach (glob("$dir/$d/*") ?: [] as $f) if (is_file($f) && basename($f) !== '.gitkeep') unlink($f);
    echo "✔ Datos anteriores eliminados\n";
}
if ((int) $pdo->query('SELECT COUNT(*) FROM galleries')->fetchColumn() > 0) {
    echo "• Ya hay galerías: no se añade la demo (usa --reset para recargarla).\n"; exit(0);
}

$demo = require ROOT . '/database/demo/demo.php';
$insG = $pdo->prepare('INSERT INTO galleries (slug,title,description,style,sort_order) VALUES (?,?,?,?,?)');
$insP = $pdo->prepare('INSERT INTO photos (gallery_id,filename,title,width,height,sort_order) VALUES (?,?,?,?,?,?)');
$photoIds = []; $galleryIds = [];

foreach ($demo['galleries'] as $gi => $g) {
    $insG->execute([$g['slug'], $g['title'], $g['description'], $g['style'], $gi]);
    $gid = (int) $pdo->lastInsertId();
    $galleryIds[$g['slug']] = $gid;
    $i = 0;
    foreach ($g['photos'] as $name => $title) {
        $src = ROOT . "/database/demo/photos/$name.webp";
        copy($src, "$dir/originals/$name.webp");                 // ya está en WebP 75%
        ImageProcessor::thumbnail($src, "$dir/thumbs/$name.webp"); // miniatura WebP 75%
        [$w, $h] = getimagesize($src);
        $insP->execute([$gid, "$name.webp", $title, $w, $h, $i++]);
        $photoIds[$name] = (int) $pdo->lastInsertId();
    }
    echo "✔ Galería: {$g['title']} ({$g['style']})\n";
}

$hero = $demo['hero'];
Setting::set('hero_type', $hero['type']);
if ($hero['type'] === 'single') Setting::set('hero_photo_id', (string) $photoIds[$hero['photo']]);
else Setting::set('hero_gallery_id', (string) $galleryIds[$hero['gallery']]);
echo "✔ Hero: {$hero['type']}\n";
