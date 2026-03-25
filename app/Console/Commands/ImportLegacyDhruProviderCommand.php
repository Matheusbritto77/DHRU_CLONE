<?php

namespace App\Console\Commands;

use App\Models\DhruProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportLegacyDhruProviderCommand extends Command
{
    protected $signature = 'dhru:import-legacy-provider';

    protected $description = 'Importa as credenciais Dhru antigas do .env para a nova tabela de provedores.';

    public function handle(): int
    {
        $url = (string) env('DHRU_API_URL');
        $username = (string) env('DHRU_API_USERNAME');
        $apiKey = (string) env('DHRU_API_APIKEY');

        if ($url === '' || $username === '' || $apiKey === '') {
            $this->error('As variaveis DHRU_API_URL, DHRU_API_USERNAME e DHRU_API_APIKEY precisam existir no .env.');

            return self::FAILURE;
        }

        $name = 'Dhru legado';

        $provider = DhruProvider::updateOrCreate(
            ['slug' => 'dhru-legado'],
            [
                'name' => $name,
                'base_url' => $url,
                'username' => $username,
                'api_key' => $apiKey,
                'is_active' => true,
                'is_default' => ! DhruProvider::query()->where('is_default', true)->exists(),
                'sync_interval_minutes' => 60,
            ]
        );

        $this->info("Provedor importado com sucesso: {$provider->name} ({$provider->slug})");

        return self::SUCCESS;
    }
}
