<?php
declare(strict_types=1);
namespace App\Services\Ai\Providers;

/** Cualquier servicio compatible con OpenAI: Groq, Together, Mistral, LM Studio, Ollama... */
final class CustomOpenAi extends OpenAiCompatible
{
    public static function label(): string        { return 'Compatible con OpenAI (URL propia)'; }
    public static function defaultModel(): string { return ''; }
    public static function needsBaseUrl(): bool   { return true; }
    public static function needsKey(): bool       { return false; }
    protected function defaultBaseUrl(): string   { return ''; }
}
