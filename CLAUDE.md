# WarpStudio / GaleriaPremium — Reglas obligatorias del proyecto

> Este archivo recoge las peticiones del propietario (David). Son de cumplimiento OBLIGATORIO en todo el proyecto.
> Cuando David diga **"anota orden"** (o lo exprese de otro modo), se añade aquí como regla obligatoria, tal cual, con numeración correlativa.

## Contexto
- Galería fotográfica premium, modular y escalable. Stack: PHP 8.3, HTML5, JavaScript, CSS y SQLite (si hace falta BD).
- Debe destacar sobre todo por su **espectacularidad**: moderna y espectacular.
- Repositorio: https://github.com/DavidJLlanes/GaleriaPremium (rama `main`).
- Producción: VPS en `/var/www/warpstudio.es` (Nginx, `root` = `/var/www/warpstudio.es/public`).

## Peticiones obligatorias
1. Construir el proyecto poco a poco, con una estructura perfecta y muy profesional.
2. Incluir un sistema de subida de fotos y varios estilos/diseños de galería.
3. Ir guardando el proyecto en el repositorio de GitHub tras cada avance.
4. **Después de CADA modificación, dar a David el comando de despliegue en su VPS** (`/var/www/warpstudio.es`).
5. Las fotos adjuntas por David son ejemplos temporales (demo).
6. Incluir diferentes diseños de galería.
7. La sección hero puede ser una galería (pase de fotos) o una sola foto a pantalla completa.
8. **Organización de código ante todo**: no engordar archivos innecesariamente. Cada componente, cada diseño de galería, cada zona de la web, cada plugin y cada sección tiene SIEMPRE sus archivos aislados (carpeta propia en `modules/` con `view.php`, `style.css`, `script.js`, `module.php`).
9. **Todas las fotos se convierten a formato WebP con calidad 75%**, en todos los casos (originales, miniaturas, subidas y demo).
10. Anotar todas las peticiones de David en este archivo; y cada vez que diga "anota orden", añadirla aquí como regla obligatoria.
11. **Cada foto se sube con nombre (título) y descripción.** La descripción puede generarse automáticamente con IA (opcional) cuando se deja vacía.
12. **Sistema de IA con APIs comunes, incluido OpenRouter** (también OpenAI, Anthropic, Gemini y endpoint compatible OpenAI personalizado). La IA genera el **título con un estilo definido en las opciones de la web** (Admin → Ajustes). Cada proveedor vive en su propio archivo (`app/Services/Ai/Providers/`).
13. **Despliegue en VPS**: la web vive en `/var/www/warpstudio.es`; Nginx apunta su `root` a `/var/www/warpstudio.es/public`. David usa Nginx puro (sin Virtualmin). Dar siempre UN solo comando, ordenado y sencillo.

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
- Tras cada cambio: commit + push a `main` y dar el comando de despliegue: `bash /var/www/warpstudio.es/deploy/deploy.sh`.
