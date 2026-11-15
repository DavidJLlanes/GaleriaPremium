#!/usr/bin/env bash
# Uso en el VPS:  bash /var/www/warpstudio.es/deploy/deploy.sh
set -euo pipefail
APP=/var/www/warpstudio.es
cd "$APP"
git pull --ff-only origin main
php bin/install.php                       # crea/actualiza esquema (idempotente)
php bin/assets.php                        # publica CSS/JS de los módulos
chown -R www-data:www-data storage public/uploads
chmod -R u+rwX,g+rwX storage public/uploads
echo "✔ Desplegado $(git rev-parse --short HEAD)"
