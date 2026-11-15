# GaleriaPremium (WarpStudio)

Galería fotográfica premium, modular. PHP 8.3 · SQLite · HTML5/CSS/JS vanilla.

## Estructura (módulos aislados)
Reglas del proyecto en [`CLAUDE.md`](CLAUDE.md).
```
public/        document root (index.php, assets publicados, uploads)
app/           núcleo: Core (Module, Assets, Page, Router...), Models, Controllers, Services
modules/       TODO lo visual, un módulo = una carpeta con view.php / style.css / script.js
  layouts/ pages/ sections/ components/ heroes/ galleries/ plugins/
config/ database/ bin/ deploy/
```
Estilos de galería: masonry · justified · grid · filmstrip. Heroes: `single` (foto) · `gallery` (pase de fotos).

## Desarrollo local
```
php bin/install.php --demo   # (--reset recarga la demo)
php -S 127.0.0.1:8000 -t public public/router.php   # sirve los assets de /modules directamente
```

## Hoja de ruta
1. ✅ Base, home espectacular, estilo masonry, lightbox
2. Panel admin (login) + subida de fotos (miniaturas, EXIF)
3. Más estilos: justified, cinematográfico, slider a pantalla completa
4. Optimización (WebP/AVIF, lazy, caché) y SEO
