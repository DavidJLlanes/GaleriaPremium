<?php
declare(strict_types=1);
namespace App\Services\Ai\Providers;

use App\Services\Ai\{AbstractProvider, AiException, Http};

final class Anthropic extends AbstractProvider
{
    public static function label(): string        { return 'Anthropic (Claude)'; }
    public static function defaultModel(): string { return 'claude-haiku-5-5'; }
    public static function keyUrl(): string       { return 'https://console.anthropic.com/settings/keys'; }

    public function models(): array
    {
        $base = rtrim($this->baseUrl ?: 'https://api.anthropic.com/v1', '/');
        $data = Http::getJson("$base/models?limit=100", ['x-api-key: ' . $this->apiKey, 'anthropic-version: 2023-06-01']);
        $out = [];
        foreach ($data['data'] ?? [] as $m) {
            if (!isset($m['id'])) continue;
            $out[] = ['id' => (string) $m['id'], 'name' => (string) ($m['display_name'] ?? $m['id']), 'vision' => true, 'price' => null];
        }
        return $out;
    }

    public function ask(string $prompt, string $jpegBase64): string
    {
        $base = rtrim($this->baseUrl ?: 'https://api.anthropic.com/v1', '/');
        $data = Http::postJson("$base/messages", [
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01',
        ], [
            'model'      => $this->model,
            'max_tokens' => 500,
            'messages'   => [[
                'role'    => 'user',
                'content' => [
                    ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => $jpegBase64]],
                    ['type' => 'text', 'text' => $prompt],
                ],
            ]],
        ]);

        $text = '';
        foreach ($data['content'] ?? [] as $part) {
            if (($part['type'] ?? '') === 'text') $text .= $part['text'];
        }
        if (trim($text) === '') throw new AiException('El modelo no devolvió texto');
        return $text;
    }
}
