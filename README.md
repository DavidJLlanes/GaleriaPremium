# GaleriaPremium (WarpStudio)

Galería fotográfica premium, modular. PHP 8.3 · SQLite · HTML5/CSS/JS vanilla.

## Estructura
```
public/        ← document root (index.php, assets, uploads)
app/Core/      Config, Database, Router, View
app/Models/    Gallery, Photo
app/Controllers/
app/Views/     layout, home, gallery, gallery/{estilo}.php
config/        config.php (+ config.local.php ignorado por git)
database/      schema.sql
bin/install.php  esquema (+ --demo)
deploy/        nginx.conf.example, deploy.sh
```
Añadir un estilo de galería = crear `app/Views/gallery/<estilo>.php` y registrarlo en `config['gallery_styles']`.

## Desarrollo local
```
php bin/install.php --demo
php -S 127.0.0.1:8000 -t public public/router.php
```

## Hoja de ruta
1. ✅ Base, home espectacular, estilo masonry, lightbox
2. Panel admin (login) + subida de fotos (miniaturas, EXIF)
3. Más estilos: justified, cinematográfico, slider a pantalla completa
4. Optimización (WebP/AVIF, lazy, caché) y SEO
