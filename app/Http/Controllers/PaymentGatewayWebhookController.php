<?php

namespace App\Http\Controllers;

use App\Support\Payments\PaymentGatewayManager;
use App\Support\Payments\PaymentWebhookProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentGatewayWebhookController extends Controller
{
    public function events(
        Request $request,
        string $gateway,
        PaymentGatewayManager $manager,
        PaymentWebhookProcessor $processor
    ): JsonResponse {
        $driver = $manager->driver($gateway);

        abort_unless($driver->validateWebhook($request), 403, 'Webhook invalido.');

        $result = $driver->handleWebhook($request);
        $processed = $processor->process($gateway, $driver, $result);

        return response()->json($processed);
    }

    public function reconcile(Request $request, string $gateway, PaymentGatewayManager $manager): JsonResponse
    {
        $secret = $request->header('X-Gateway-Webhook-Secret', $request->string('secret')->toString());

        abort_unless($manager->validateWebhookSecret($gateway, $secret), 403, 'Webhook secret invalido.');

        $result = $manager->reconcile($gateway)[$gateway] ?? ['checked' => 0, 'updated' => 0];

        return response()->json([
            'gateway' => $gateway,
            'checked' => (int) ($result['checked'] ?? 0),
            'updated' => (int) ($result['updated'] ?? 0),
        ]);
    }
}
