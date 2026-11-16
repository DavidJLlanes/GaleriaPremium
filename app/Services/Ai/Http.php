<?php
declare(strict_types=1);
namespace App\Services\Ai;

/** Cliente HTTP mínimo (cURL) compartido por todos los proveedores. */
final class Http
{
    public static function getJson(string $url, array $headers = [], int $timeout = 20): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS, CURLOPT_FOLLOWLOCATION => false, CURLOPT_MAXFILESIZE => 20_000_000,
            CURLOPT_HTTPHEADER => array_merge(['Accept: application/json'], $headers),
        ]);
        $raw = curl_exec($ch); $err = curl_error($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($raw === false) throw new AiException('Error de red: ' . $err);
        $data = json_decode((string) $raw, true);
        if ($code >= 400) {
            $msg = is_array($data) ? ($data['error']['message'] ?? $data['error'] ?? $data['message'] ?? $raw) : $raw;
            if (is_array($msg)) $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            throw new AiException("HTTP $code: " . mb_substr((string) $msg, 0, 300));
        }
        if (!is_array($data)) throw new AiException('Respuesta no válida del proveedor');
        return $data;
    }

    public static function postJson(string $url, array $headers, array $body, int $timeout = 60): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_PROTOCOLS      => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => array_merge(['Content-Type: application/json'], $headers),
            CURLOPT_POSTFIELDS     => json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
        $raw  = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) throw new AiException('Error de red: ' . $err);
        $data = json_decode((string) $raw, true);

        if ($code >= 400) {
            $msg = is_array($data) ? ($data['error']['message'] ?? $data['error'] ?? $data['message'] ?? $raw) : $raw;
            if (is_array($msg)) $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            throw new AiException("HTTP $code: " . mb_substr((string) $msg, 0, 300));
        }
        if (!is_array($data)) throw new AiException('Respuesta no válida del proveedor');
        return $data;
    }
}
