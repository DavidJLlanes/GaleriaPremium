#!/usr/bin/env bash
# Uso en el VPS:  bash /var/www/warpstudio.es/deploy/deploy.sh
# Opcional: WEB_USER=www-data bash deploy/deploy.sh   (usuario con el que corre PHP-FPM)
set -euo pipefail
APP="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP"

git config --global --add safe.directory "$APP" 2>/dev/null || true
git pull --ff-only origin main
php bin/install.php          # crea/actualiza el esquema (idempotente)
php bin/assets.php           # publica el CSS/JS de los módulos

# Permisos: PHP debe poder escribir en la BD y en las fotos
OWNER="${WEB_USER:-$(stat -c %U "$APP")}"
[ "$OWNER" = "root" ] && OWNER="www-data"
chown -R "$OWNER":"$(id -gn "$OWNER")" storage public/uploads public/assets
chmod -R u+rwX,g+rwX storage public/uploads
echo "✔ Desplegado $(git rev-parse --short HEAD) (propietario: $OWNER)"
