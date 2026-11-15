<?php
declare(strict_types=1);
namespace App\Services\Ai\Providers;

use App\Core\Config;

final class OpenRouter extends OpenAiCompatible
{
    public static function label(): string        { return 'OpenRouter'; }
    public static function defaultModel(): string { return 'google/gemini-2.5-flash'; }
    public static function keyUrl(): string       { return 'https://openrouter.ai/keys'; }
    protected function defaultBaseUrl(): string   { return 'https://openrouter.ai/api/v1'; }

    protected function extraHeaders(): array
    {
        return ['X-Title: ' . Config::get('app.name', 'WarpStudio')];
    }
}
