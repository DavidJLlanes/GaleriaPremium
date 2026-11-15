<?php
declare(strict_types=1);
namespace App\Services\Ai\Providers;

final class OpenAi extends OpenAiCompatible
{
    public static function label(): string        { return 'OpenAI'; }
    public static function defaultModel(): string { return 'gpt-4o-mini'; }
    public static function keyUrl(): string       { return 'https://platform.openai.com/api-keys'; }
    protected function defaultBaseUrl(): string   { return 'https://api.openai.com/v1'; }
    protected function tokenParam(): string       { return 'max_completion_tokens'; }
}
