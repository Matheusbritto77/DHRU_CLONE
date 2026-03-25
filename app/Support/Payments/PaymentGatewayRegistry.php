<?php

namespace App\Support\Payments;

use App\Models\Plugin;
use Illuminate\Support\Collection;
use RuntimeException;

class PaymentGatewayRegistry
{
    /**
     * @return array<string, class-string<\App\Contracts\Payments\PaymentGatewayDriver>>
     */
    public function drivers(): array
    {
        return config('payment-gateways.drivers', []);
    }

    /**
     * @return array<int, string>
     */
    public function slugs(): array
    {
        return array_keys($this->drivers());
    }

    public function plugin(string $slug): Plugin
    {
        $plugin = Plugin::query()->where('slug', $slug)->first();

        if (! $plugin) {
            throw new RuntimeException("Plugin de gateway [{$slug}] nao encontrado.");
        }

        return $plugin;
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(string $slug): array
    {
        $plugin = $this->plugin($slug);

        return array_replace_recursive(
            $plugin->default_settings ?? [],
            $plugin->adminSettings?->settings ?? [],
        );
    }

    /**
     * @return Collection<int, Plugin>
     */
    public function plugins(): Collection
    {
        return Plugin::query()
            ->whereIn('slug', $this->slugs())
            ->orderBy('name')
            ->get();
    }
}
