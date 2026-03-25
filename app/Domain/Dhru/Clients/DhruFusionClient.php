<?php

namespace App\Domain\Dhru\Clients;

use App\Domain\Dhru\Contracts\DhruProviderClientInterface;
use App\Models\DhruProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DhruFusionClient implements DhruProviderClientInterface
{
    public function fetchServiceCatalog(DhruProvider $provider): array
    {
        return $this->request($provider, 'imeiservicelist', []);
    }

    public function request(DhruProvider $provider, string $action, array|string $parameters, bool $rawParameters = false): array
    {
        $payload = [
            'username' => $provider->username,
            'apiaccesskey' => $provider->api_key,
            'requestformat' => 'JSON',
            'action' => $action,
        ];

        if ($action !== 'imeiservicelist') {
            $payload['parameters'] = $rawParameters
                ? $parameters
                : $this->normalizeParameters($parameters);
        }

        $response = Http::asForm()
            ->timeout(30)
            ->post($this->normalizeBaseUrl($provider->base_url), $payload);

        $body = $response->body();

        if (! $response->successful()) {
            \Illuminate\Support\Facades\Log::error('Dhru API Error', [
                'provider' => $provider->name,
                'status' => $response->status(),
                'body' => $body,
            ]);
            throw new RuntimeException('Falha ao consultar o provedor Dhru: HTTP ' . $response->status());
        }

        // Tenta limpar o corpo da resposta de possíveis BOM ou espaços em branco/caracteres invisíveis
        $cleanBody = trim($body);
        
        // Se a resposta começar com algo que não seja { ou [, tentamos encontrar o início do JSON
        if (!str_starts_with($cleanBody, '{') && !str_starts_with($cleanBody, '[')) {
            $firstBrace = strpos($cleanBody, '{');
            $firstBracket = strpos($cleanBody, '[');
            
            $start = false;
            if ($firstBrace !== false && $firstBracket !== false) {
                $start = min($firstBrace, $firstBracket);
            } elseif ($firstBrace !== false) {
                $start = $firstBrace;
            } elseif ($firstBracket !== false) {
                $start = $firstBracket;
            }

            if ($start !== false) {
                $cleanBody = substr($cleanBody, $start);
            }
        }

        $decoded = json_decode($cleanBody, true);

        if (! is_array($decoded)) {
            \Illuminate\Support\Facades\Log::error('Dhru Invalid Response', [
                'provider' => $provider->name,
                'url' => $this->normalizeBaseUrl($provider->base_url),
                'action' => $action,
                'raw_body' => $body,
            ]);
            throw new RuntimeException('Resposta Dhru invalida. O servidor não retornou um JSON válido.');
        }

        return $decoded;
    }

    protected function normalizeBaseUrl(string $url): string
    {
        $url = rtrim($url, '/');

        // Se a URL termina com /index.php, assume que já está completa
        if (str_ends_with($url, '/index.php')) {
            return $url;
        }

        // Se não tem /api, adiciona. Se já tem, remove para garantir o padrão final /api/index.php
        $url = str_replace('/api', '', $url);

        return $url . '/api/index.php';
    }

    protected function normalizeParameters(array|string $parameters): string
    {
        if (is_string($parameters)) {
            return $parameters;
        }

        $xml = '<PARAMETERS>';

        foreach ($parameters as $key => $value) {
            $xml .= '<' . $key . '>' . e((string) $value) . '</' . $key . '>';
        }

        $xml .= '</PARAMETERS>';

        return html_entity_decode($xml, ENT_QUOTES, 'UTF-8');
    }
}
