<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImeiService;
use App\Models\server_services;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DhruWebhookController extends Controller
{
    /**
     * Handle the incoming DHRU webhook.
     * 
     * Expected parameters from DHRU:
     * - ID: The Reference ID sent by our server
     * - STATUS: The status code (3=Rejected, 4=Success, etc.)
     * - REPLY: The unlock code or rejection reason
     */
    public function handle(Request $request)
    {
        Log::info('DHRU Webhook received.', ['payload' => $request->all()]);

        $referenceId = $request->input('ID');
        $status = (int) $request->input('STATUS');
        $reply = $request->input('REPLY');

        if (!$referenceId) {
            return response()->json(['error' => 'Missing ID'], 400);
        }

        // Try to find the order in ImeiService (IMEI)
        $order = ImeiService::where('referenceid', $referenceId)->first();
        
        // If not found, try in server_services (Server)
        if (!$order) {
            $order = server_services::where('referenceid', $referenceId)->first();
        }

        if (!$order) {
            Log::warning('DHRU Webhook: Order not found.', ['referenceid' => $referenceId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Check if status actually changed
        if ((int) $order->status === $status) {
             return response()->json(['message' => 'Status unchanged'], 200);
        }

        // Handle Refund if status is 3 (Rejected/Refunded)
        if ($status === 3 && stripos((string) $order->servicename, 'No Refund') === false) {
            $this->refundUser($order->user, (float) $order->cost);
        }

        // Update the order
        $order->update([
            'status' => $status,
            'code' => $reply,
        ]);

        Log::info('DHRU Webhook: Order updated.', [
            'order_id' => $order->id,
            'new_status' => $status,
            'referenceid' => $referenceId
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Refund credits to the user.
     */
    protected function refundUser($user, float $amount): void
    {
        if (!$user) return;

        $user->increment('credit', $amount);
        
        Log::info('DHRU Webhook: User refunded.', [
            'user_id' => $user->id,
            'amount' => $amount
        ]);
    }
}
