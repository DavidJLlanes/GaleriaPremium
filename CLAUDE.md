# WarpStudio / GaleriaPremium — Reglas obligatorias del proyecto

> Este archivo recoge las peticiones del propietario (David). Son de cumplimiento OBLIGATORIO en todo el proyecto.
> Cuando David diga **"anota orden"** (o lo exprese de otro modo), se añade aquí como regla obligatoria, tal cual, con numeración correlativa.

## Contexto
- Galería fotográfica premium, modular y escalable. Stack: PHP 8.3, HTML5, JavaScript, CSS y SQLite (si hace falta BD).
- Debe destacar sobre todo por su **espectacularidad**: moderna y espectacular.
- Repositorio: https://github.com/DavidJLlanes/GaleriaPremium (rama `main`).
- Producción (dominio **luxaeterna.es**; antes estuvo en warpstudio.es): VPS en `/var/www/luxaeterna.es` (Nginx, `root` = `/var/www/luxaeterna.es/public`).

## Peticiones obligatorias
1. Construir el proyecto poco a poco, con una estructura perfecta y muy profesional.
2. Incluir un sistema de subida de fotos y varios estilos/diseños de galería.
3. Ir guardando el proyecto en el repositorio de GitHub tras cada avance.
4. **Después de CADA modificación, dar a David el comando de despliegue en su VPS** (`/var/www/luxaeterna.es`).
5. Las fotos adjuntas por David son ejemplos temporales (demo).
6. Incluir diferentes diseños de galería.
7. La sección hero puede ser una galería (pase de fotos) o una sola foto a pantalla completa.
8. **Organización de código ante todo**: no engordar archivos innecesariamente. Cada componente, cada diseño de galería, cada zona de la web, cada plugin y cada sección tiene SIEMPRE sus archivos aislados (carpeta propia en `modules/` con `view.php`, `style.css`, `script.js`, `module.php`).
9. **Todas las fotos se convierten a formato WebP con calidad 75%**, en todos los casos (originales, miniaturas, subidas y demo).
10. Anotar todas las peticiones de David en este archivo; y cada vez que diga "anota orden", añadirla aquí como regla obligatoria.
11. **Cada foto se sube con nombre (título) y descripción.** La descripción puede generarse automáticamente con IA (opcional) cuando se deja vacía.
12. **Sistema de IA con APIs comunes, incluido OpenRouter** (también OpenAI, Anthropic, Gemini y endpoint compatible OpenAI personalizado). La IA genera el **título con un estilo definido en las opciones de la web** (Admin → Ajustes). Cada proveedor vive en su propio archivo (`app/Services/Ai/Providers/`).
13. **Despliegue en VPS**: la web vive en `/var/www/luxaeterna.es`; Nginx apunta su `root` a `/var/www/luxaeterna.es/public`. David usa Nginx puro (sin Virtualmin). Dar siempre UN solo comando, ordenado y sencillo.
14. **Cada galería tiene su propio diseño visual** (selector en la gestión de la galería) y las galerías y la sección hero se pueden **ordenar** desde el panel.
15. **Barra superior opcional** en la pantalla principal (ajuste de Sitio).
16. **Identidad del sitio / webapp**: favicon, icono, logo, nombre y más opciones en Admin → Sitio. Al subir un icono se generan automáticamente los iconos adaptativos (iPhone, Android, PC). Excepción documentada a la regla 9: los iconos (PNG, por requisito de plataforma) y el logo; las fotos siguen siendo WebP 75%.
17. **Galería "slider" horizontal**: todas las fotos con la misma proporción, definida por la primera; en el resto el usuario elige el recorte con esa proporción. Opción para ajustar la separación entre fotos y que no se corte ninguna a ningún ancho de pantalla.
18. **Lightbox con efecto configurable por cada galería** (fade, slide, flip, curtain, zoom; cada efecto en `modules/lightboxes/<nombre>/`). La foto se ajusta al tamaño y proporciones de la pantalla activa.
19. **Subida global por arrastre**: arrastrar una foto a cualquier zona de la web la sube, mostrando título, descripción y galería (igual que la subida normal, siempre vía `ImageProcessor`).
20. **Gestión masiva en el panel**: borrado masivo de fotos y de galerías, y mover fotos entre galerías (selección múltiple con casillas; componente `components/bulk`).
21. **Panel «Fotos»**: sección de administración para gestionar todas las fotos de todas las galerías (filtro, búsqueda, mover y borrar en bloque).

## Arquitectura (cómo cumplir la regla 8)
```
modules/<tipo>/<nombre>/{view.php, style.css, script.js, module.php}
  tipos: layouts · pages · sections · components · heroes · galleries · plugins
app/Core/Module.php   render()/use(): al usar un módulo se registran SOLO sus CSS/JS
app/Core/Assets.php   emite <link>/<script> de los módulos usados en la página
app/Services/ImageProcessor.php   ÚNICO punto de conversión de fotos (WebP 75%)
```
- Nuevo diseño de galería → carpeta `modules/galleries/<nombre>/` (aparece solo; se elige en `galleries.style`).
- Nuevo hero → `modules/heroes/<nombre>/` (se elige con el ajuste `hero_type`).
- Los CSS/JS de un módulo solo afectan a ese módulo (clases con prefijo propio). Nada de CSS/JS global salvo `layouts/main`.
- Toda subida o importación de fotos DEBE pasar por `ImageProcessor` (WebP 75%).
- Tras cada cambio: commit + push a `main` y dar el comando de despliegue: `bash /var/www/luxaeterna.es/deploy/deploy.sh`.
