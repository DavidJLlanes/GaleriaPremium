<?php
declare(strict_types=1);
namespace App\Services\Ai;

/** Construye el prompt a partir de las opciones de la web (idioma y estilos). */
final class PromptBuilder
{
    public const DEFAULT_TITLE_STYLE = 'Poético, breve y evocador (de 2 a 5 palabras)';
    public const DEFAULT_DESC_STYLE  = 'Descriptiva y sobria, en una o dos frases';

    public static function build(string $language, string $titleStyle, string $descStyle, string $currentTitle = ''): string
    {
        $p  = "Eres el editor de una galería fotográfica premium. Observa la fotografía y responde ÚNICAMENTE con un objeto JSON válido, sin texto adicional ni bloques de código, con esta forma exacta:\n";
        $p .= '{"title": "...", "description": "..."}' . "\n\n";
        $p .= "Idioma de la respuesta: $language.\n";
        $p .= "ESTILO DEL TÍTULO: " . ($titleStyle ?: self::DEFAULT_TITLE_STYLE) . ". Máximo 8 palabras, sin comillas, sin punto final y sin emojis.\n";
        $p .= "ESTILO DE LA DESCRIPCIÓN: " . ($descStyle ?: self::DEFAULT_DESC_STYLE) . ". Describe lo que realmente se ve; no inventes nombres propios, lugares ni datos que no sean evidentes.\n";
        if ($currentTitle !== '') $p .= "La foto ya se llama \"$currentTitle\"; úsalo como contexto para la descripción.\n";
        return $p;
    }

    /** Extrae {title, description} de la respuesta del modelo (tolera ```json ... ``` y texto alrededor). */
    public static function parse(string $raw): array
    {
        $raw = trim($raw);
        $a = strpos($raw, '{'); $b = strrpos($raw, '}');
        if ($a === false || $b === false || $b <= $a) throw new AiException('La IA no devolvió JSON');
        $j = json_decode(substr($raw, $a, $b - $a + 1), true);
        if (!is_array($j)) throw new AiException('La IA devolvió un JSON no válido');

        $clean = static fn ($v) => trim(trim((string) $v), "\"'“”«» \t\n\r.");
        return [
            'title'       => $clean($j['title'] ?? $j['titulo'] ?? ''),
            'description' => trim((string) ($j['description'] ?? $j['descripcion'] ?? '')),
        ];
    }
}
