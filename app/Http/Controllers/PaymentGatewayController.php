<?php

namespace App\Http\Controllers;

use App\Support\Payments\PaymentGatewayManager;
use App\Support\Payments\PaymentQuoteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentGatewayController extends Controller
{
    public function checkout(
        Request $request,
        string $gateway,
        PaymentGatewayManager $manager,
        PaymentQuoteService $quoteService
    ): RedirectResponse|View
    {
        $request->validate([
            'credit_amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $driver = $manager->driver($gateway);
        $settings = $driver->settings();
        $quote = $quoteService->buildQuote((float) $request->input('credit_amount'), $request->user(), [
            ...$settings,
            'global_fee_rate' => (float) $request->input('global_fee_rate', 0),
        ]);

        $payload = array_merge($request->all(), [
            'credit_amount' => $quote['credit_amount'],
            'amount_usd_hidden' => $quote['credit_amount'],
            'total_brl' => $quote['gateway_currency'] === 'BRL'
                ? $quote['total_gateway']
                : $quoteService->convert($quote['total_gateway'], $quote['gateway_currency'], 'BRL'),
            'total_gateway' => $quote['total_gateway'],
            'gateway_currency' => $quote['gateway_currency'],
            'display_total' => $quote['display_total'],
            'user_currency' => $quote['user_currency'],
            'quote' => $quote,
        ]);

        if ((bool) ($settings['manual_checkout'] ?? false)) {
            $manualUrl = (string) ($settings['manual_checkout_url'] ?? '');
            $manualMessage = (string) ($settings['manual_checkout_message'] ?? 'Entre em contato para concluir este pagamento.');

            if ($manualUrl !== '') {
                return redirect()->away($manualUrl);
            }

            return view('payments.manual', [
                'gatewayLabel' => $settings['display_name'] ?? $settings['checkout_label'] ?? $gateway,
                'message' => $manualMessage,
                'quote' => $quote,
            ]);
        }

        $result = $driver->createCheckout($payload, $request->user());

        if (($result['type'] ?? null) === 'redirect') {
            return redirect()->away((string) $result['target']);
        }

        return view((string) $result['view'], $result['data'] ?? []);
    }
}
