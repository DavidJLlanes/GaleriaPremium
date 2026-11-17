<?php
declare(strict_types=1);
/** Regenera los iconos de la webapp desde el icono ya subido (idempotente): php bin/icons.php */
require __DIR__ . '/../app/bootstrap.php';
echo App\Services\Brand\IconBuilder::rebuild() ? "✔ Iconos de la webapp regenerados\n" : "· Sin icono subido: nada que regenerar\n";
