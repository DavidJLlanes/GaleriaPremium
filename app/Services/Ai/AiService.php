<?php
declare(strict_types=1);
namespace App\Services\Ai;

use App\Models\Setting;
use App\Services\Ai\Providers\{Anthropic, CustomOpenAi, Gemini, OpenAi, OpenRouter};

/**
 * Punto único de acceso a la IA. Lee la configuración de los ajustes de la web
 * (tabla settings) y delega en el proveedor elegido.
 */
final class AiService
{
    /** id => clase. Añadir un proveedor = un archivo en Providers/ + una línea aquí. */
    public const PROVIDERS = [
        'openrouter' => OpenRouter::class,
        'openai'     => OpenAi::class,
        'anthropic'  => Anthropic::class,
        'gemini'     => Gemini::class,
        'custom'     => CustomOpenAi::class,
    ];

    public static function settings(): array
    {
        $provider = Setting::get('ai_provider', 'openrouter');
        if (!isset(self::PROVIDERS[$provider])) $provider = 'openrouter';
        $class = self::PROVIDERS[$provider];
        return [
            'provider'    => $provider,
            'key'         => Setting::get("ai_key_$provider"),
            'model'       => Setting::get("ai_model_$provider") ?: $class::defaultModel(),
            'base_url'    => Setting::get("ai_base_url_$provider"),
            'language'    => Setting::get('ai_language', 'español'),
            'title_style' => Setting::get('ai_title_style', PromptBuilder::DEFAULT_TITLE_STYLE),
            'desc_style'  => Setting::get('ai_desc_style', PromptBuilder::DEFAULT_DESC_STYLE),
            'auto'        => Setting::get('ai_auto', '1') === '1',
        ];
    }

    public static function isConfigured(): bool
    {
        $s = self::settings(); $class = self::PROVIDERS[$s['provider']];
        if ($s['model'] === '') return false;
        if ($class::needsKey() && $s['key'] === '') return false;
        if ($class::needsBaseUrl() && $s['base_url'] === '') return false;
        return true;
    }

    private static function provider(array $s): AbstractProvider
    {
        $class = self::PROVIDERS[$s['provider']];
        return new $class($s['key'], $s['model'], $s['base_url'] ?: null);
    }

    /**
     * Genera título y descripción para la imagen $path (cualquier formato que GD lea).
     * @return array{title:string, description:string}
     */
    public static function describe(string $path, string $currentTitle = ''): array
    {
        if (!self::isConfigured()) throw new AiException('La IA no está configurada (Ajustes → Inteligencia artificial)');
        $s = self::settings();
        $prompt = PromptBuilder::build($s['language'], $s['title_style'], $s['desc_style'], $currentTitle);
        $raw = self::provider($s)->ask($prompt, self::jpegBase64($path));
        return PromptBuilder::parse($raw);
    }

    /**
     * Modelos disponibles de un proveedor. Usa los datos del formulario si vienen; si no, los guardados.
     * @return list<array{id:string,name:string,vision:?bool,price:?string}>
     */
    public static function listModels(string $provider, string $key = '', string $baseUrl = ''): array
    {
        if (!isset(self::PROVIDERS[$provider])) throw new AiException('Proveedor desconocido');
        $class = self::PROVIDERS[$provider];
        $key = $key !== '' ? $key : Setting::get("ai_key_$provider");
        $baseUrl = $baseUrl !== '' ? $baseUrl : Setting::get("ai_base_url_$provider");
        if ($class::needsKey() && $key === '' && $provider !== 'openrouter') throw new AiException('Pega primero la API key de este proveedor');
        if ($class::needsBaseUrl() && $baseUrl === '') throw new AiException('Indica primero la URL base de la API');
        return (new $class($key, '', $baseUrl ?: null))->models();
    }

    /** Prueba de conexión con una imagen sintética. Devuelve la respuesta tal cual. */
    public static function test(): array
    {
        $img = imagecreatetruecolor(96, 96);
        for ($y = 0; $y < 96; $y++) {
            imageline($img, 0, $y, 95, $y, imagecolorallocate($img, 255 - $y * 2, 120 + $y, 60 + $y));
        }
        $tmp = tempnam(sys_get_temp_dir(), 'ai') . '.png';
        imagepng($img, $tmp);
        try { return self::describe($tmp); } finally { @unlink($tmp); }
    }

    /** JPEG reducido (lado mayor 1024) en base64: menos tokens y más rápido. */
    private static function jpegBase64(string $path): string
    {
        $img = @imagecreatefromstring((string) file_get_contents($path));
        if (!$img) throw new AiException('No se pudo leer la imagen');
        $w = imagesx($img); $h = imagesy($img);
        if (max($w, $h) > 1024) {
            $k = 1024 / max($w, $h);
            $img = imagescale($img, (int) round($w * $k), (int) round($h * $k), IMG_BICUBIC);
        }
        ob_start(); imagejpeg($img, null, 80); return base64_encode((string) ob_get_clean());
    }
}
