<?php
declare(strict_types=1);
namespace App\Core;

final class Config
{
    private static array $data = [];

    public static function load(array $data): void { self::$data = $data; }

    /** Sobrescribe un valor en tiempo de ejecución (p. ej. el nombre de la web guardado en ajustes). */
    public static function set(string $key, mixed $value): void
    {
        $ref = &self::$data;
        foreach (explode('.', $key) as $part) {
            if (!isset($ref[$part])) $ref[$part] = [];
            $ref = &$ref[$part];
        }
        $ref = $value;
    }

    /** Acceso con notación de puntos: Config::get('db.path') */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$data;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) return $default;
            $value = $value[$part];
        }
        return $value;
    }
}
