#!/usr/bin/env bash
# Instalación completa en el VPS (se puede repetir sin problema). Ejecutar como root:
#   bash /var/www/warpstudio.es/deploy/setup-vps.sh [usuario_admin]
set -euo pipefail

APP="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CONF=/etc/nginx/sites-available/warpstudio.es
BACKUP=/root/warpstudio.es.nginx.bak
ADMIN="${1:-david}"

[ "$(id -u)" -eq 0 ] || { echo "Ejecuta como root"; exit 1; }
cd "$APP"

echo "▶ 1/5 Extensiones de PHP (gd, sqlite3, curl, mbstring)"
PHPV="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
apt-get update -qq
apt-get install -y -qq "php$PHPV-gd" "php$PHPV-sqlite3" "php$PHPV-curl" "php$PHPV-mbstring"
systemctl restart "php$PHPV-fpm"

echo "▶ 2/5 Base de datos y datos de ejemplo (solo si aún no hay galerías)"
mkdir -p log
php bin/install.php --demo

echo "▶ 3/5 Nginx (root en public/, solo index.php se ejecuta)"
[ -f "$BACKUP" ] || cp "$CONF" "$BACKUP"
cp deploy/nginx.conf.example "$CONF"
if ! nginx -t; then
    cp "$BACKUP" "$CONF"
    echo "✗ La configuración nueva no es válida: he restaurado la anterior."
    exit 1
fi
systemctl reload nginx

echo "▶ 4/5 Publicar assets y ajustar permisos"
bash deploy/deploy.sh

echo "▶ 5/5 Administrador '$ADMIN' (te pedirá la contraseña, mínimo 8 caracteres)"
php bin/admin.php "$ADMIN"

echo
echo "✔ Todo listo → https://warpstudio.es   ·   Panel: https://warpstudio.es/admin"
