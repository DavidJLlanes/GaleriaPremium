# Plugins

Cada plugin vive en su propia carpeta `modules/plugins/<nombre>/` con sus archivos aislados
(`view.php`, `style.css`, `script.js`, `module.php`). Se activan renderizándolos desde una página
o sección con `Module::render('plugins/<nombre>')`. Ningún plugin debe editar archivos de otro módulo.
