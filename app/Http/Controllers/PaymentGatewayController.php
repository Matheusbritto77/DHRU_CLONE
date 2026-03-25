<?php

namespace App\Http\Controllers;

use App\Support\Payments\PaymentGatewayManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentGatewayController extends Controller
{
    public function checkout(Request $request, string $gateway, PaymentGatewayManager $manager): RedirectResponse|View
    {
        $request->validate([
            'amount_usd_hidden' => ['required', 'numeric', 'min:0.01'],
            'total_brl' => ['required', 'numeric', 'min:0.01'],
        ]);

        $result = $manager->driver($gateway)->createCheckout($request->all(), $request->user());

        if (($result['type'] ?? null) === 'redirect') {
            return redirect()->away((string) $result['target']);
        }

        return view((string) $result['view'], $result['data'] ?? []);
    }
}
