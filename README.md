<div align="center">

# GaleriaPremium · Lux Aeterna

**Galería fotográfica premium, modular y espectacular — sin frameworks, sin dependencias.**

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-3-003B57?style=for-the-badge&logo=sqlite&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES2022-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Nginx](https://img.shields.io/badge/Nginx-009639?style=for-the-badge&logo=nginx&logoColor=white)

![WebP](https://img.shields.io/badge/im%C3%A1genes-WebP%20q75-4285F4?style=flat-square)
![WebAuthn](https://img.shields.io/badge/acceso-Passkeys%20%2F%20WebAuthn-3b82f6?style=flat-square)
![PWA](https://img.shields.io/badge/web%20app-PWA%20instalable-5A0FC8?style=flat-square)
![WebGL](https://img.shields.io/badge/efectos-WebGL%20%2B%20View%20Transitions-ff6b35?style=flat-square)
![Fuentes](https://img.shields.io/badge/tipograf%C3%ADas-148%20locales-111827?style=flat-square)
![Dependencias](https://img.shields.io/badge/dependencias-0-22c55e?style=flat-square)
![Arquitectura](https://img.shields.io/badge/arquitectura-modular-d8b36a?style=flat-square)

[Características](#-características) ·
[Arquitectura](#-arquitectura-modular) ·
[Instalación local](#-instalación-en-local) ·
[Despliegue en VPS](#-despliegue-en-un-vps) ·
[Panel](#-panel-de-administración) ·
[Seguridad](#-seguridad) ·
[Ampliar](#-cómo-ampliar-el-proyecto)

</div>

---

## ✨ Qué es

**GaleriaPremium** es un portafolio fotográfico pensado para impresionar: portadas animadas, galerías con diseños muy distintos entre sí, un lightbox cinematográfico, transiciones entre páginas y una identidad visual totalmente configurable… todo desde un panel de administración propio.

Está construido con **PHP 8.3 puro, SQLite y HTML/CSS/JS vanilla**: sin Composer, sin npm en producción, sin frameworks y sin recursos de terceros (ni CDN ni Google Fonts). Se despliega con **un solo comando** en cualquier VPS con Nginx.

> **Principio rector — organización de código ante todo.** Cada diseño de galería, cada portada, cada componente y cada sección vive aislado en su propia carpeta con sus archivos (`view.php`, `style.css`, `script.js`, `module.php`). Añadir un diseño nuevo es soltar una carpeta; no hay que tocar nada más.

---

## 🚀 Características

### 🖼️ Galerías y fotos
| | |
|---|---|
| **10 diseños de galería** | `masonry` · `justified` · `grid` · `filmstrip` · `lumina` · `tunnel` · `slider` · `baraja` · `bento` · `morph` — cada galería elige el suyo |
| **Lightbox con 5 efectos** | `fade` · `slide` · `flip` · `curtain` · `zoom` — configurable por galería, la foto se adapta a la pantalla |
| **Lightbox ampliado** | zoom con rueda, doble toque y pellizco; arrastrar para cerrar; miniaturas; descripción; datos de cámara (EXIF); resplandor con el color medio de la foto |
| **Subida con título y descripción** | la descripción puede generarla una IA si se deja vacía |
| **Subida global por arrastre** | suelta una foto en cualquier zona de la web (con sesión de administrador) y se sube |
| **Todo en WebP 75 %** | originales, miniaturas, subidas y demo pasan por un único punto de conversión (`ImageProcessor`) |
| **Carga «desenfoque a nítido»** | miniatura WebP de 28 px incrustada (LQIP) que se enfoca al cargar la foto real |
| **Galería «fijada» y «destacada»** | la fijada va siempre la primera; las destacadas llevan insignia y marco de acento |
| **Orden por arrastre** | también en móvil (táctil) |

### 🎞️ Sliders
Sliders ilimitados con **6 diseños** (`clásico`, `cinematográfico`, `marco`, `miniaturas`, `carrusel`, `centrado 3D`), **6 transiciones** (desvanecer, deslizar, zoom, difuminar, cortina, giro 3D), **4 estilos de flechas** y **4 de puntos guía**. Todas las fotos comparten la proporción de la primera subida; el resto se recorta con esa proporción.

### 🏠 Portada, tarjetas y navegación
- **20 estilos de portada** (hero): cortina, parallax 3D, Ken Burns, linterna, texto-máscara, mosaico, tarjetas, cinta, scroll expansivo, split, editorial, marco minimal, contador, agua (**WebGL**), **vídeo** de fondo, y dos de **pluma de luz** que escriben el título trazo a trazo con la tipografía elegida (esqueletizado Zhang-Suen + orden caligráfico). También «foto única», «pase de fotos» o ninguna portada.
- **Botón de la portada**: 7 diseños, 5 tamaños y separación configurables, con vista previa en vivo.
- **Pantalla de entrada** breve con 6 diseños (luz, fundido, letras, cortina, contador, línea).
- **Sección de galerías** con 6 composiciones (rejilla, carrusel con inercia, índice editorial, pantallas apiladas, editorial alterno, acordeón), **8 efectos de aparición** al hacer scroll y **6 diseños de título** de tarjeta.
- **Menú «hamburguesa» de galerías** con 6 iconos, 6 efectos de despliegue y 5 diseños de menú.
- **Pie de página** totalmente personalizable: 5 diseños × 6 efectos de llegada, redes (solo con el usuario), contacto, firma y modal de información legal.
- **Barra superior opcional** con colores propios y tinte de logo.

### 🏷️ Categorías
Hasta **3 categorías por foto**, reutilizables mediante desplegable. Cada galería y slider decide si las muestra; el indicador tiene **7 diseños y 5 tamaños**. Gestión completa: crear, renombrar, unir, mover todas las fotos de una a otra, borrar con o sin sus fotos, limpiar vacías y acciones en bloque.

### 🎨 Identidad y tipografía
- **148 tipografías alojadas en local** (serif, sans, display, manuscritas, monoespaciadas) con selector con buscador que muestra cada fuente con su propia letra; peso y cursiva por tipo de texto; **fuentes propias** subibles (`.woff2/.woff/.ttf/.otf`).
- **Webapp instalable (PWA)**: favicon, logo e **iconos adaptativos** (iPhone, Android *maskable*, Windows) generados automáticamente desde un único icono.
- Color de acento, nombre, eslogan, redes y datos para buscadores desde el panel.

### ⚡ Efectos «premium» (activables)
Scroll con inercia · cursor propio (6 estilos) · grano · transiciones entre páginas (View Transitions API) · sonidos suaves. Todo respeta `prefers-reduced-motion`.

### 🤖 IA opcional
Genera **títulos y descripciones** con el estilo definido en el panel. Proveedores: **OpenRouter**, **OpenAI**, **Anthropic**, **Google Gemini** y cualquier **endpoint compatible con OpenAI**. Cada proveedor vive en su propio archivo.

### 🔐 Acceso
Contraseña **o huella / Face ID / Windows Hello** (passkeys WebAuthn, sin dependencias: solo se guarda la clave pública).

---

## 🧱 Arquitectura modular

Toda la parte visual son **módulos**: una carpeta por pieza. Al renderizar un módulo se registran **solo** su CSS y JS, que se publican como estáticos con caché larga.

```
GaleriaPremium/
├── public/                  # document root (único directorio expuesto)
│   ├── index.php            # front controller + rutas
│   ├── assets/              # CSS/JS de los módulos publicados (bin/assets.php)
│   ├── fonts/               # 148 tipografías .woff2 (subset latin)
│   └── uploads/             # fotos WebP, iconos, fuentes propias, vídeo de portada
├── app/
│   ├── Core/                # Router, Module, Assets, Page, Database, Security…
│   ├── Models/              # Gallery, Photo, User
│   ├── Controllers/         # públicos + Admin/
│   └── Services/            # ImageProcessor, Auth, Passkeys, Categories, Ai/…
├── modules/                 # TODO lo visual: view.php · style.css · script.js · module.php
│   ├── layouts/ pages/ sections/ components/ (51)
│   ├── heroes/ (20)  galleries/ (10)  lightboxes/ (5)  sliders/ (6)
│   ├── slider-fx/ slider-arrows/ slider-dots/
│   ├── home-layouts/ reveals/ card-titles/ intros/ cursors/
│   └── menu-icons/ menu-reveals/ menu-styles/ footers/ footer-reveals/
├── config/                  # config.php (+ config.local.php, no versionado) y catálogo de fuentes
├── database/                # schema.sql, migrate.php (migraciones idempotentes) y datos demo
├── bin/                     # install · admin · assets · backfill · icons
├── deploy/                  # deploy.sh · setup-vps.sh · nginx.conf.example · teardown-vps.sh
├── tools/fonts/             # generador del catálogo tipográfico (Node, solo desarrollo)
└── CLAUDE.md                # reglas obligatorias del proyecto
```

**Flujo de una petición:** `public/index.php` → `Router` → controlador → `Page::render('modules/…')` → `Module` registra los assets usados → `Assets` los inyecta en el `<head>` del layout.

---

## 🧰 Stack y requisitos

| Capa | Tecnología |
|---|---|
| Backend | **PHP 8.3** (`strict_types`), PDO |
| Base de datos | **SQLite 3** (un único archivo, `foreign_keys` activas) |
| Frontend | **HTML5 · CSS3 · JavaScript vanilla** (ES modules no necesarios), WebGL, View Transitions, IntersectionObserver |
| Servidor | **Nginx** + PHP-FPM, HTTPS con Let's Encrypt |
| Imágenes | **GD** con soporte WebP |
| Autenticación | Sesiones + **WebAuthn** (ES256/RS256 con OpenSSL) |
| Herramientas dev | Node.js (solo para regenerar el catálogo de fuentes) |

**Extensiones de PHP:** `gd` (con WebP), `sqlite3`/`pdo_sqlite`, `curl`, `mbstring`, `openssl`, `fileinfo`.

---

## 💻 Instalación en local

```bash
git clone https://github.com/DavidJLlanes/GaleriaPremium.git
cd GaleriaPremium

php bin/install.php --demo          # crea la BD y carga datos de ejemplo (--reset recarga la demo)
php bin/admin.php <usuario>         # crea el administrador (pide la contraseña, mínimo 8 caracteres)

php -S localhost:8000 -d upload_max_filesize=40M -d post_max_size=48M -t public public/router.php
```

- Web: <http://localhost:8000> · Panel: <http://localhost:8000/admin>
- El router de desarrollo sirve los assets directamente desde `modules/`, no hace falta publicarlos.
- Para probar **passkeys** en local usa `localhost` (WebAuthn no admite direcciones IP).

---

## 🌍 Despliegue en un VPS

La web vive en `/var/www/<dominio>` y Nginx apunta su `root` a `/var/www/<dominio>/public`. **Solo `index.php` se ejecuta**; los uploads nunca se interpretan como código.

### Primera instalación (una vez, como root)

```bash
git clone https://github.com/DavidJLlanes/GaleriaPremium.git /var/www/luxaeterna.es
bash /var/www/luxaeterna.es/deploy/setup-vps.sh <usuario_admin>
```

`setup-vps.sh` es **idempotente** y hace, en orden:

1. Instala las extensiones de PHP (`gd`, `sqlite3`, `curl`, `mbstring`) y reinicia PHP-FPM.
2. Crea la base de datos y los datos de ejemplo (solo si aún no hay galerías).
3. Genera la configuración de **Nginx** desde `deploy/nginx.conf.example` (detecta si hay certificado de Let's Encrypt), la valida con `nginx -t` y, si falla, **restaura la anterior**.
4. Publica los assets y ajusta permisos (`deploy.sh`).
5. Crea el administrador (te pide la contraseña).

> El dominio se deduce del nombre de la carpeta (`/var/www/luxaeterna.es` → `luxaeterna.es`).

### Actualizar (tras cada cambio)

```bash
bash /var/www/luxaeterna.es/deploy/deploy.sh
```

`deploy.sh` es idempotente y ejecuta: `git pull --ff-only` → `bin/install.php` (esquema y migraciones) → `bin/backfill.php` (datos derivados de fotos antiguas) → `bin/icons.php` (regenera los iconos de la webapp) → `bin/assets.php` (publica CSS/JS) → ajusta propietario y permisos (detecta el usuario de PHP-FPM; se puede forzar con `WEB_USER=www-data`) y protege la BD y la configuración local.

### Configuración del servidor

`deploy/nginx.conf.example` incluye:
- `root` en `public/` y front controller `index.php`.
- **Caché larga `immutable`** para estáticos (con `?v=` de versión) y **gzip**.
- Bloqueo de ficheros ocultos y de cualquier `.php` que no sea `index.php`.
- Redirección a HTTPS con certificado de Let's Encrypt.

Para sobrescribir ajustes sin tocar el repositorio crea `config/config.local.php` (se ignora en git):

```php
<?php return [
    'app'   => ['name' => 'Mi Galería', 'debug' => false],
    'image' => ['quality' => 75, 'max_edge' => 2400],
];
```

### Retirar la web
`deploy/teardown-vps.sh` desinstala el proyecto del dominio (sin backup; el repositorio de GitHub no se toca).

---

## 🛠️ Panel de administración

La navegación sigue el orden en que aparece la web:

| Grupo | Secciones |
|---|---|
| **Contenido** | Galerías · Sliders · Fotos · Categorías |
| **La web, de arriba abajo** | Entrada · Barra y menú · Portada · Tarjetas · Pie |
| **Global** | Sitio (identidad, colores, tipografías, webapp, redes) · Efectos · IA · Acceso (passkeys) |

Gestión masiva (borrar, mover, categorizar), subida con arrastre, orden por arrastre y **vistas previas en vivo** de portada, entrada, menú, pie y tarjetas (la propia web en un iframe).

---

## 🔒 Seguridad

- **Cabeceras:** CSP estricta (sin recursos de terceros), HSTS, `X-Frame-Options`, `Cross-Origin-Opener-Policy`, `Permissions-Policy` y `Referrer-Policy`; errores genéricos sin filtrar detalles.
- **Autenticación:** contraseñas con hash, límite de intentos por IP, sesión `HttpOnly` + `SameSite=Strict` + `Secure`, regeneración de ID al entrar; passkeys con reto de un solo uso y comprobación de origen, `rpId`, firma y contador.
- **CSRF** en todo `POST` del panel y consultas **siempre preparadas** (PDO).
- **Escapado** de todo texto en vistas y JSON incrustado protegido contra `</script>`.
- **Subidas:** el tipo se decide por la cabecera real del archivo (nunca por la extensión), límite de megapíxeles y de tamaño, y todas las fotos se reprocesan a WebP.
- **Privacidad:** los datos EXIF se guardan **sin coordenadas GPS**.
- Una auditoría con pruebas dinámicas (rutas sin sesión, sin CSRF, entradas malformadas, XSS almacenado) acompaña cada gran cambio.

---

## ⚙️ Rendimiento

Imágenes WebP con miniaturas y LQIP · estáticos con caché `immutable` y gzip · CSS/JS **solo de los módulos usados** en cada página · animaciones con `transform`/`opacity`/`clip-path` · autoplay y efectos pausados fuera de pantalla · `prefers-reduced-motion` respetado en todo.

---

## 🧩 Cómo ampliar el proyecto

Cada tipo de pieza es una carpeta; aparece sola en el panel.

| Quiero añadir… | Creo… |
|---|---|
| Un diseño de galería | `modules/galleries/<nombre>/` |
| Un estilo de portada | `modules/heroes/<nombre>/` |
| Un efecto de lightbox | `modules/lightboxes/<nombre>/` |
| Un diseño de slider / transición / flechas / puntos | `modules/sliders/`, `slider-fx/`, `slider-arrows/`, `slider-dots/` |
| Un cursor, una entrada, un pie… | `modules/cursors/`, `intros/`, `footers/`… |
| Un proveedor de IA | `app/Services/Ai/Providers/<Nombre>.php` |
| Una tipografía | `tools/fonts/list.json` + `node tools/fonts/build.mjs` (o súbela desde el panel) |

Un módulo mínimo:

```
modules/galleries/mi-diseno/
├── module.php   # etiqueta, descripción y ajustes propios
├── view.php     # recibe $gallery y $photos
├── style.css    # solo afecta a este módulo (clases con prefijo propio)
└── script.js
```

**Convenciones** (resumen; las reglas completas están en [`CLAUDE.md`](CLAUDE.md)):
- CSS/JS aislados por módulo; nada global salvo `layouts/`.
- Toda foto pasa por `ImageProcessor` (WebP 75 %).
- Tipografías siempre con variables (`var(--f-title)`), nunca un nombre fijo.
- Prefijo `pnl-` en el panel y **nunca** nombres como `ad`, `banner` o `sponsor` (los bloqueadores de contenido ocultan la página).
- Movimiento lento y suave y soporte de `prefers-reduced-motion`.

---

## 🗺️ Estado del proyecto

- [x] Galerías (10 diseños), lightbox (5 efectos) y sliders (6 diseños)
- [x] 20 portadas, entrada, tarjetas, menú y pie configurables
- [x] Panel completo con subida, gestión masiva, categorías y vistas previas
- [x] IA multi-proveedor para títulos y descripciones
- [x] Tipografías locales, fuentes propias e identidad / PWA con iconos adaptativos
- [x] Efectos premium y passkeys (huella / Face ID)
- [x] Despliegue en un comando y auditoría de seguridad

---

<div align="center">

Hecho con cuidado por **David** · León, España

[![GitHub](https://img.shields.io/badge/GitHub-DavidJLlanes-181717?style=flat-square&logo=github)](https://github.com/DavidJLlanes)

</div>
