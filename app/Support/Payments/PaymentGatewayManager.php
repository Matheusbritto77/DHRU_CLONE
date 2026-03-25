<?php

namespace App\Support\Payments;

use App\Contracts\Payments\PaymentGatewayDriver;
use App\Models\Plugin;
use Illuminate\Support\Collection;
use RuntimeException;

class PaymentGatewayManager
{
    public function __construct(
        protected PaymentGatewayRegistry $registry,
    ) {
    }

    public function driver(string $slug): PaymentGatewayDriver
    {
        $class = $this->registry->drivers()[$slug] ?? null;

        if (! $class) {
            throw new RuntimeException("Gateway [{$slug}] nao registrado.");
        }

        return app($class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function activeGateways(): array
    {
        return Plugin::query()
            ->whereIn('slug', $this->registry->slugs())
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Plugin $plugin): array {
                $driver = $this->driver($plugin->slug);
                $settings = $this->registry->settings($plugin->slug);

                return [
                    'slug' => $plugin->slug,
                    'label' => ($settings['checkout_label'] ?? null) ?: (($settings['display_name'] ?? null) ?: $plugin->name),
                    'display_name' => ($settings['display_name'] ?? null) ?: $plugin->name,
                    'enabled' => $driver->isEnabled(),
                ];
            })
            ->filter(fn (array $gateway) => $gateway['enabled'])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function adminGateways(): array
    {
        return $this->registry->plugins()
            ->map(function (Plugin $plugin): array {
                $settings = $this->registry->settings($plugin->slug);

                return [
                    'slug' => $plugin->slug,
                    'name' => $plugin->name,
                    'enabled' => (bool) ($settings['enabled'] ?? false),
                    'reconcile_webhook_url' => route('payments.reconcile.webhook', ['gateway' => $plugin->slug]),
                    'checkout_url' => route('payments.checkout', ['gateway' => $plugin->slug]),
                ];
            })
            ->values()
            ->all();
    }

    public function validateWebhookSecret(string $slug, ?string $secret): bool
    {
        $configuredSecret = (string) ($this->registry->settings($slug)['reconcile_webhook_secret'] ?? '');

        return $configuredSecret !== '' && hash_equals($configuredSecret, (string) $secret);
    }

    public function reconcile(?string $slug = null): array
    {
        if ($slug) {
            return [$slug => $this->driver($slug)->reconcilePending()];
        }

        return collect($this->registry->slugs())
            ->mapWithKeys(fn (string $gatewaySlug) => [$gatewaySlug => $this->driver($gatewaySlug)->reconcilePending()])
            ->all();
    }
}
