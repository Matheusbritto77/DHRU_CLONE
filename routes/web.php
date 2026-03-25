<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IMEIController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\PaymentGatewayWebhookController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\AddCreditsController;
use Livewire\Livewire;






Route::get('/livewire/livewire/dist/livewire.js', function () {
    return app(\Livewire\Controllers\HttpConnectionHandler::class)->getScript(request());
})->name('livewire.js');





Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/custom/livewire/update', $handle); // Exemplo de middleware adicionado
});







Route::get('/', function () {
    $page = \App\Models\Page::where('slug', 'welcome')->where('is_active', true)->first();

    if (!$page) {
        abort(404, 'Página não encontrada.');
    }

    $blocks = $page->blocks()
        ->where('is_visible', true)
        ->with('plugin')
        ->orderBy('sort_order')
        ->get();

    return view('welcome', compact('page', 'blocks'));
});


Route::middleware(['auth:sanctum', 'can:view-admin'])->group(function () {
    // Rota delegada para o painel Filament v3
    // Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    
    
    Route::post('/update-service-fields', [AdminController::class, 'updateServiceFields'])->name('edit_service');
    
    
  Route::post('/admin/update-cost-percentage', [AdminController::class, 'updateCostPercentage'])->name('admin.update-cost-percentage');
    
Route::get('admin/imei-service', [AdminController::class, 'showIMEIServices'])->name('Admin.services');


Route::get('admin/history', [AdminController::class, 'showIMEIHistory'])->name('admin.order.history');


});






Route::get('/buscar-servicos-imei', [IMEIController::class, 'buscarServicosIMEI'])->name('buscar_servicos_imei');







Route::post('/payments/{gateway}/checkout', [PaymentGatewayController::class, 'checkout'])
    ->middleware(['auth', 'verified'])
    ->name('payments.checkout');

Route::post('/webhooks/payments/{gateway}/reconcile', [PaymentGatewayWebhookController::class, 'reconcile'])
    ->name('payments.reconcile.webhook');

Route::post('/processar-pagamento', [PaymentGatewayController::class, 'checkout'])
    ->middleware(['auth', 'verified'])
    ->defaults('gateway', 'payment-binance-pay')
    ->name('processarPagamento');

Route::view('/pagamento', 'pagamento')->name('pagamento');

// Exemplo de rota para a página de confirmação
Route::get('/confirmacao-pagamento', function () {
    return view('confirmacao');
})->name('paginaConfirmacao');



Route::get('/api/get-all-services', [ApiController::class, 'getAllServices']);

// Rota para exibir o formulário de configuração da API
Route::get('/configurar-api', [ApiController::class, 'configurarApiForm'])->name('configurar.api.form');

// Rota para salvar as configurações da API
Route::post('/salvar-configuracoes-api', [ApiController::class, 'salvarConfiguracoesApi'])->name('salvar.configuracoes.api');
Route::any('/obter-informacoes-conta', [ApiController::class, 'obterInformacoesConta'])->name('obter.informacoes.conta');


Route::get('/imei', [IMEIController::class, 'showIMEIServices'])->name('imei.index');
Route::get('/imei-service', [IMEIController::class, 'index'])->name('imei.services');
Route::get('/search-imei-services', [IMEIController::class, 'search'])->name('search.imei.services');



Route::middleware(['auth'])->group(function () {
    // Rota para lidar com o envio do formulário de Serial Number
    Route::post('/submit-serial-number', [ApiController::class, 'enviarOrdemSerialNumber'])->name('submit_serial_number');
    Route::get('/get-imei-order/{orderID}', [ApiController::class, 'getIMEIOrder'])->name('get_imei_order');
    Route::get('/check-imei-orders', [ApiController::class, 'checkAndUpdateIMEIOrdersStatus'])->name('check.imei.orders');
    Route::get('/check-server', [ApiController::class, 'server'])->name('check.server.orders');
    Route::get('/imei-history', [IMEIController::class, 'showIMEIHistory'])->name('imei.history');
    Route::get('/server-services', [ServerController::class, 'showServerServices'])->name('server-services');
    Route::post('/tes-api', [ApiController::class, 'enviarOrdemAPIserver'])->name('test.api');
    Route::post('/submit-server', [ApiController::class, 'enviarOrdemServer'])->name('submit_server');
    Route::get('/Server-history', [ServerController::class, 'showIMEIHistory'])->name('Server.history');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');




Route::post('/pix', [PaymentGatewayController::class, 'checkout'])
    ->middleware(['auth', 'verified'])
    ->defaults('gateway', 'payment-gerencianet-pix')
    ->name('process.pix');
Route::match(['GET', 'POST'], '/pix/status', [PaymentGatewayWebhookController::class, 'reconcile'])
    ->defaults('gateway', 'payment-gerencianet-pix')
    ->name('process.pix.status');
// routes/web.php
// Define a rota que retorna diretamente a view
Route::get('/add-credits', [AddCreditsController::class, 'index'])->name('add-credits');
