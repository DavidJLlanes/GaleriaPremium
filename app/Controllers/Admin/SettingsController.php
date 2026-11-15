<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Page, Response};
use App\Models\Setting;
use App\Services\{Auth, Csrf, Flash};
use App\Services\Ai\{AiException, AiService, PromptBuilder};

final class SettingsController
{
    public function show(array $p = []): void
    {
        Auth::guard();
        $providers = [];
        foreach (AiService::PROVIDERS as $id => $class) {
            $providers[$id] = [
                'label' => $class::label(), 'model' => Setting::get("ai_model_$id") ?: $class::defaultModel(),
                'default_model' => $class::defaultModel(), 'key_set' => Setting::get("ai_key_$id") !== '',
                'key_url' => $class::keyUrl(), 'needs_base_url' => $class::needsBaseUrl(), 'needs_key' => $class::needsKey(),
                'base_url' => Setting::get("ai_base_url_$id"),
            ];
        }
        Page::render('pages/admin-settings', [
            'ai' => AiService::settings(), 'providers' => $providers,
            'defaults' => ['title' => PromptBuilder::DEFAULT_TITLE_STYLE, 'desc' => PromptBuilder::DEFAULT_DESC_STYLE],
        ], ['title' => 'Ajustes', 'nav' => 'ajustes'], 'layouts/admin');
    }

    public function save(array $p = []): void
    {
        Auth::guard(); Csrf::verify();
        $provider = (string) ($_POST['ai_provider'] ?? 'openrouter');
        if (!isset(AiService::PROVIDERS[$provider])) $provider = 'openrouter';
        Setting::set('ai_provider', $provider);
        Setting::set('ai_auto', isset($_POST['ai_auto']) ? '1' : '0');
        Setting::set('ai_language', trim((string) ($_POST['ai_language'] ?? '')) ?: 'español');
        Setting::set('ai_title_style', trim((string) ($_POST['ai_title_style'] ?? '')));
        Setting::set('ai_desc_style', trim((string) ($_POST['ai_desc_style'] ?? '')));

        foreach (array_keys(AiService::PROVIDERS) as $id) {
            $key = trim((string) ($_POST["ai_key_$id"] ?? ''));
            if ($key !== '') Setting::set("ai_key_$id", $key);                // vacío = conservar la actual
            if (isset($_POST["ai_clear_$id"])) Setting::set("ai_key_$id", '');
            Setting::set("ai_model_$id", trim((string) ($_POST["ai_model_$id"] ?? '')));
            Setting::set("ai_base_url_$id", trim((string) ($_POST["ai_base_url_$id"] ?? '')));
        }
        Flash::set('Ajustes guardados.');
        Response::redirect('/admin/ajustes');
    }

    /** AJAX: prueba la conexión con la configuración YA guardada. */
    public function test(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        try {
            $r = AiService::test();
            Response::json(['ok' => true] + $r);
        } catch (AiException $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 502);
        }
    }
}
