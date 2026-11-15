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

    protected function describeModel(array $m): ?array
    {
        $in = $m['architecture']['input_modalities'] ?? [];
        $out = $m['architecture']['output_modalities'] ?? ['text'];
        if (!in_array('text', $out, true)) return null;               // sin modelos de imagen/audio puros
        $p = $m['pricing']['prompt'] ?? null; $c = $m['pricing']['completion'] ?? null;
        $price = (is_numeric($p) && is_numeric($c))
            ? ((float) $p == 0.0 && (float) $c == 0.0 ? 'gratis' : sprintf('$%s / $%s por 1M', rtrim(rtrim(number_format((float) $p * 1e6, 3, '.', ''), '0'), '.'), rtrim(rtrim(number_format((float) $c * 1e6, 3, '.', ''), '0'), '.')))
            : null;
        return ['id' => (string) $m['id'], 'name' => (string) ($m['name'] ?? $m['id']), 'vision' => in_array('image', $in, true), 'price' => $price];
    }

    protected function extraHeaders(): array
    {
        return ['X-Title: ' . Config::get('app.name', 'WarpStudio')];
    }
}
