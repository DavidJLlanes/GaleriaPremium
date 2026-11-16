<?php
declare(strict_types=1);
/** Rellena los datos derivados de las fotos existentes (miniatura de carga). Idempotente: php bin/backfill.php */
require __DIR__ . '/../app/bootstrap.php';
echo '✔ Placeholders de carga generados: ' . App\Services\Lqip::backfill() . "\n";
