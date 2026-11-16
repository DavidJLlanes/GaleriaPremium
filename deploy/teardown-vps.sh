#!/usr/bin/env bash
# Retira WarpStudio de /var/www/warpstudio.es y deja el dominio libre para otro proyecto. SIN backup.
# Se queda solo la carpeta log/. El repositorio de GitHub no se toca.
#   bash /var/www/warpstudio.es/deploy/teardown-vps.sh
set -euo pipefail

# Se copia a /tmp y se relanza desde allí: así puede borrar la carpeta donde vive sin cortarse a sí mismo.
if [ "${TEARDOWN_COPY:-}" != 1 ]; then
    cp "$0" /tmp/teardown-warpstudio.sh
    TEARDOWN_COPY=1 exec bash /tmp/teardown-warpstudio.sh "$@"
fi

APP="${APP_DIR:-/var/www/warpstudio.es}"
SITE="${NGINX_SITE:-/etc/nginx/sites-available/warpstudio.es}"
ETC="${ETC_DIR:-/etc}"
[ "$(id -u)" -eq 0 ] || [ -n "${APP_DIR:-}" ] || { echo "Ejecuta como root"; exit 1; }
[ -d "$APP" ] || { echo "No existe $APP"; exit 1; }

echo "▶ 1/4 Borrando todo de $APP excepto log/"
mkdir -p "$APP/log"
find "$APP" -mindepth 1 -maxdepth 1 ! -name log -exec rm -rf {} +

echo "▶ 2/4 Nginx: configuración neutra del dominio (SSL y logs se conservan)"
cat > "$SITE" <<'NGINX'
# warpstudio.es — dominio libre, listo para otro proyecto
server {
    listen 80;
    listen [::]:80;
    server_name warpstudio.es www.warpstudio.es;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name warpstudio.es www.warpstudio.es;

    root /var/www/warpstudio.es;
    index index.html index.php;

    ssl_certificate /etc/letsencrypt/live/warpstudio.es/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/warpstudio.es/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    access_log /var/www/warpstudio.es/log/access.log;
    error_log  /var/www/warpstudio.es/log/error.log;

    location / { try_files $uri $uri/ =404; }
    location ~ /\.(?!well-known) { deny all; }
}
NGINX
rm -f /root/warpstudio.es.nginx.bak
if [ -z "${APP_DIR:-}" ]; then nginx -t && systemctl reload nginx; fi

echo "▶ 3/4 Servicios, tareas programadas y pools asociados"
found=0
for f in "$ETC"/systemd/system/*warpstudio* "$ETC"/cron.d/*warpstudio* "$ETC"/logrotate.d/*warpstudio* "$ETC"/php/*/fpm/pool.d/*warpstudio* "$ETC"/supervisor/conf.d/*warpstudio*; do
    [ -e "$f" ] || continue
    found=1; echo "   · eliminado $f"
    case "$f" in *.service|*.timer) systemctl disable --now "$(basename "$f")" 2>/dev/null || true ;; esac
    rm -f "$f"
done
[ "$found" = 1 ] && [ -z "${APP_DIR:-}" ] && { systemctl daemon-reload; systemctl reload "php*-fpm" 2>/dev/null || true; }
[ "$found" = 0 ] && echo "   · no había ninguno"
git config --global --unset-all safe.directory "$APP" 2>/dev/null || true

echo "▶ 4/4 Comprobación"
ls -A "$APP"
rm -f /tmp/teardown-warpstudio.sh
echo "✔ $APP queda solo con log/ y el dominio listo para otro proyecto."
