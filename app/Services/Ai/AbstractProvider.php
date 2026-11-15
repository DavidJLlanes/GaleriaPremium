<?php
declare(strict_types=1);
namespace App\Services\Ai;

/**
 * Contrato de un proveedor de IA con visión.
 * Para añadir uno nuevo: crear Providers/<Nombre>.php que extienda esta clase
 * y registrarlo en AiService::PROVIDERS. Un proveedor = un archivo.
 */
abstract class AbstractProvider
{
    public function __construct(
        protected string $apiKey,
        protected string $model,
        protected ?string $baseUrl = null,
    ) {}

    /** Nombre visible en el panel. */
    abstract public static function label(): string;

    /** Modelo sugerido (editable por el usuario en los ajustes). */
    abstract public static function defaultModel(): string;

    /** Enlace donde conseguir la API key (se muestra en ajustes). */
    public static function keyUrl(): string { return ''; }

    /** ¿Necesita URL base propia? (proveedor genérico compatible con OpenAI) */
    public static function needsBaseUrl(): bool { return false; }

    /** ¿La API key es obligatoria? */
    public static function needsKey(): bool { return true; }

    /** Envía prompt + imagen JPEG (base64) y devuelve el texto de respuesta. */
    abstract public function ask(string $prompt, string $jpegBase64): string;
}
