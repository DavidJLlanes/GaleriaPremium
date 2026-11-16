#!/usr/bin/env bash
# Instalación completa en el VPS (se puede repetir sin problema). Ejecutar como root:
#   bash /var/www/<dominio>/deploy/setup-vps.sh [usuario_admin]
# El dominio se deduce del nombre de la carpeta (p. ej. /var/www/luxaeterna.es → luxaeterna.es).
set -euo pipefail

APP="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DOMAIN="$(basename "$APP")"
CONF="/etc/nginx/sites-available/$DOMAIN"
BACKUP="/root/$DOMAIN.nginx.bak"
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
[ -f "$CONF" ] && { [ -f "$BACKUP" ] || cp "$CONF" "$BACKUP"; }
if [ -d "/etc/letsencrypt/live/$DOMAIN" ]; then SSL=1; DROP='#NOSSL#'; KEEP='#SSL#'; else SSL=0; DROP='#SSL#'; KEEP='#NOSSL#'; fi
sed -e "/^$DROP/d" -e "s/^$KEEP//" -e "s/__DOMAIN__/$DOMAIN/g" deploy/nginx.conf.example > "$CONF"
ln -sf "$CONF" "/etc/nginx/sites-enabled/$DOMAIN"
if ! nginx -t; then
    if [ -f "$BACKUP" ]; then cp "$BACKUP" "$CONF"; echo "✗ La configuración nueva no es válida: he restaurado la anterior."; else rm -f "$CONF" "/etc/nginx/sites-enabled/$DOMAIN"; echo "✗ La configuración nueva no es válida: la he retirado."; fi
    exit 1
fi
systemctl reload nginx

echo "▶ 4/5 Publicar assets y ajustar permisos"
bash deploy/deploy.sh

echo "▶ 5/5 Administrador '$ADMIN' (te pedirá la contraseña, mínimo 8 caracteres)"
php bin/admin.php "$ADMIN"

echo "▶ Permisos finales (la BD la toca el usuario de PHP-FPM, no root)"
bash deploy/deploy.sh

echo
if [ "$SSL" = 1 ]; then echo "✔ Todo listo → https://$DOMAIN   ·   Panel: https://$DOMAIN/admin"
else echo "✔ Instalado en http://$DOMAIN (aún sin HTTPS). Activa el certificado con:"; echo "   certbot --nginx -d $DOMAIN -d www.$DOMAIN"; fi
