<?php
declare(strict_types=1);
namespace App\Services\Ai\Providers;

use App\Services\Ai\{AbstractProvider, AiException, Http};

final class Gemini extends AbstractProvider
{
    public static function label(): string        { return 'Google Gemini'; }
    public static function defaultModel(): string { return 'gemini-2.5-flash'; }
    public static function keyUrl(): string       { return 'https://aistudio.google.com/apikey'; }

    public function ask(string $prompt, string $jpegBase64): string
    {
        $base = rtrim($this->baseUrl ?: 'https://generativelanguage.googleapis.com/v1beta', '/');
        $data = Http::postJson("$base/models/" . rawurlencode($this->model) . ':generateContent', [
            'x-goog-api-key: ' . $this->apiKey,
        ], [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    ['inline_data' => ['mime_type' => 'image/jpeg', 'data' => $jpegBase64]],
                ],
            ]],
            'generationConfig' => ['maxOutputTokens' => 800, 'responseMimeType' => 'application/json'],
        ]);

        $text = '';
        foreach ($data['candidates'][0]['content']['parts'] ?? [] as $part) $text .= $part['text'] ?? '';
        if (trim($text) === '') throw new AiException('El modelo no devolvió texto');
        return $text;
    }
}
