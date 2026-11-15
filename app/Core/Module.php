<?php
declare(strict_types=1);
namespace App\Core;

/**
 * Un MÓDULO es una carpeta aislada en /modules/{tipo}/{nombre}/ con:
 *   view.php    (obligatorio al renderizar) HTML del módulo
 *   style.css   (opcional) estilos SOLO de este módulo
 *   script.js   (opcional) JS SOLO de este módulo
 *   module.php  (opcional) metadatos: ['label' => '...']
 *
 * Al renderizar o "usar" un módulo, sus assets se registran solos.
 */
final class Module
{
    public const DIR = ROOT . '/modules';

    private static function check(string $id): void
    {
        if (!preg_match('#^[a-z0-9-]+/[a-z0-9-]+$#', $id)) {
            throw new \InvalidArgumentException("Id de módulo inválido: $id");
        }
    }

    public static function path(string $id): string
    {
        self::check($id);
        return self::DIR . '/' . $id;
    }

    public static function exists(string $id): bool
    {
        return (bool) preg_match('#^[a-z0-9-]+/[a-z0-9-]+$#', $id) && is_file(self::path($id) . '/view.php');
    }

    /** Registra los assets del módulo sin renderizarlo (p. ej. comportamientos puros). */
    public static function use(string $id): void
    {
        $dir = self::path($id);
        if (is_file("$dir/style.css")) Assets::addCss($id);
        if (is_file("$dir/script.js")) Assets::addJs($id);
    }

    public static function render(string $id, array $data = []): string
    {
        $file = self::path($id) . '/view.php';
        if (!is_file($file)) throw new \RuntimeException("Módulo no encontrado: $id");
        self::use($id);
        return (static function (string $__file, array $__data): string {
            extract($__data, EXTR_SKIP);
            ob_start();
            require $__file;
            return (string) ob_get_clean();
        })($file, $data);
    }

    public static function meta(string $id): array
    {
        $f = self::path($id) . '/module.php';
        return is_file($f) ? (array) require $f : [];
    }

    /** Lista los módulos de un tipo: ['masonry' => ['label'=>...], ...] */
    public static function all(string $kind): array
    {
        $out = [];
        foreach (glob(self::DIR . "/$kind/*/view.php") ?: [] as $view) {
            $name = basename(dirname($view));
            $out[$name] = self::meta("$kind/$name") + ['label' => ucfirst($name)];
        }
        ksort($out);
        return $out;
    }
}
