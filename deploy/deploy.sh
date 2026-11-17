#!/usr/bin/env bash
# Uso en el VPS:  bash /var/www/luxaeterna.es/deploy/deploy.sh
# Opcional: WEB_USER=www-data bash deploy/deploy.sh   (usuario con el que corre PHP-FPM)
set -euo pipefail
APP="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP"

git config --global --add safe.directory "$APP" 2>/dev/null || true
git pull --ff-only origin main
php bin/install.php          # crea/actualiza el esquema (idempotente)
php bin/backfill.php         # datos derivados de las fotos existentes (idempotente)
php bin/icons.php            # iconos de la webapp (iPhone/Android) con el algoritmo actual
php bin/assets.php           # publica el CSS/JS de los módulos

# Permisos: PHP debe poder escribir en la BD y en las fotos
# (se detecta el usuario con el que corre PHP-FPM; se puede forzar con WEB_USER=...)
OWNER="${WEB_USER:-$(ps -eo user:32,comm | awk '$2 ~ /php-fpm/ && $1 != "root" {print $1; exit}')}"
OWNER="${OWNER:-www-data}"
chown -R "$OWNER":"$(id -gn "$OWNER")" storage public/uploads public/assets
chmod -R u+rwX,g+rwX storage public/uploads
# La base de datos y la configuración local (claves de IA) no deben ser legibles por otros usuarios del servidor
chmod -R o-rwx storage
[ -f config/config.local.php ] && chmod 640 config/config.local.php || true
echo "✔ Desplegado $(git rev-parse --short HEAD) (propietario: $OWNER)"
