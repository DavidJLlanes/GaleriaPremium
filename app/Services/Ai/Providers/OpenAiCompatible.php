<?php
declare(strict_types=1);
namespace App\Services\Ai\Providers;

use App\Services\Ai\{AbstractProvider, AiException, Http};

/** Base para toda API con formato "chat/completions" de OpenAI (OpenAI, OpenRouter, Groq, Ollama...). */
abstract class OpenAiCompatible extends AbstractProvider
{
    abstract protected function defaultBaseUrl(): string;

    protected function extraHeaders(): array { return []; }
    protected function tokenParam(): string { return 'max_tokens'; }

    public function ask(string $prompt, string $jpegBase64): string
    {
        $base    = rtrim($this->baseUrl ?: $this->defaultBaseUrl(), '/');
        $headers = $this->extraHeaders();
        if ($this->apiKey !== '') $headers[] = 'Authorization: Bearer ' . $this->apiKey;

        $data = Http::postJson("$base/chat/completions", $headers, [
            'model'    => $this->model,
            'messages' => [[
                'role'    => 'user',
                'content' => [
                    ['type' => 'text', 'text' => $prompt],
                    ['type' => 'image_url', 'image_url' => ['url' => 'data:image/jpeg;base64,' . $jpegBase64]],
                ],
            ]],
            $this->tokenParam() => 500,
        ]);

        $content = $data['choices'][0]['message']['content'] ?? null;
        if (is_array($content)) {   // algunos modelos devuelven partes
            $content = implode('', array_map(static fn ($p) => $p['text'] ?? '', $content));
        }
        if (!is_string($content) || trim($content) === '') throw new AiException('El modelo no devolvió texto');
        return $content;
    }
}
