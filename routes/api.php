<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\DhruWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// DHRU Fusion Webhook for Status Updates
Route::post('dhru/webhook', [DhruWebhookController::class, 'handle']);
