<?php

namespace App\Http\Controllers;

use App\Domain\Dhru\Services\DhruOrderService;
use App\Jobs\ProcessImeiOrder;
use App\Jobs\ProcessServerOrder;
use App\Models\DhruCatalogService;
use App\Models\ImeiService;
use App\Models\server_services;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function configurarApiForm()
    {
        return view('api.configurar_api');
    }

    public function salvarConfiguracoesApi()
    {
        return response()->json([
            'message' => 'As configuracoes agora sao gerenciadas em /admin/dhru-providers.',
        ]);
    }

    public function obterInformacoesConta()
    {
        return response()->json([
            'message' => 'Cadastre e teste a conta diretamente no recurso de provedores Dhru no admin.',
        ]);
    }

    public function getAllServices()
    {
        return response()->json([
            'message' => 'O catalogo agora vem da sincronizacao de provedores em /admin/dhru-providers.',
        ], 200);
    }

    public function enviarOrdemSerialNumber(Request $request, DhruOrderService $dhruOrderService)
    {
        Log::info('Recebendo pedido de Serial Number.', ['request' => $request->all()]);

        $validatedData = $request->validate([
            'SERIAL_NUMBER' => 'required|string',
            'SERVICEID' => 'required',
            'CATALOG_SERVICE_ID' => 'required|integer|exists:dhru_catalog_services,id',
            'PROVIDER_ID' => 'required|integer|exists:dhru_providers,id',
            'servicename' => 'required|string',
        ]);
        
        Log::info('Dados de Serial Number validados.', ['validatedData' => $validatedData]);

        $catalogService = $this->resolveCatalogService($validatedData, 'IMEI');

        if (! $this->verificarCreditosSuficientes((float) $catalogService->cost)) {
            Log::warning('Creditos insuficientes para Serial Number.', ['user_id' => Auth::id(), 'cost' => $catalogService->cost]);
            return response()->json(['error' => 'Créditos insuficientes para realizar a ordem.'], 422);
        }

        $response = $dhruOrderService->placeImeiOrder($catalogService, $validatedData['SERIAL_NUMBER']);

        return $this->processarRespostaImei($response, $validatedData, $catalogService);
    }

    public function enviarOrdemServer(Request $request, DhruOrderService $dhruOrderService)
    {
        Log::info('Recebendo pedido de Server.', ['request' => $request->all()]);

        // Limpa string vazias que vêm do JS para que o Laravel interprete como null
        if ($request->input('Qnt') === '') {
            $request->merge(['Qnt' => null]);
        }
        if ($request->input('PROVIDER_ID') === '') {
            $request->merge(['PROVIDER_ID' => null]);
        }
        if ($request->input('CATALOG_SERVICE_ID') === '') {
            $request->merge(['CATALOG_SERVICE_ID' => null]);
        }

        $validatedData = $request->validate([
            'SERVICEID' => 'required',
            'CATALOG_SERVICE_ID' => 'required|integer|exists:dhru_catalog_services,id',
            'PROVIDER_ID' => 'required|integer|exists:dhru_providers,id',
            'servicename' => 'required|string',
            'username' => 'nullable|string',
            'Qnt' => 'nullable|integer|min:1',
            'SERIAL_NUMBER' => 'nullable|string',
        ]);
        
        Log::info('Dados de Server validados.', ['validatedData' => $validatedData]);

        $catalogService = $this->resolveCatalogService($validatedData, 'SERVER');
        $quantity = (int) ($validatedData['Qnt'] ?? 0);
        $calculatedCost = $quantity > 0
            ? ((float) $catalogService->cost * $quantity)
            : (float) $catalogService->cost;

        if (! $this->verificarCreditosSuficientes($calculatedCost)) {
            return response()->json(['error' => 'Créditos insuficientes para realizar a ordem.'], 422);
        }

        $response = $dhruOrderService->placeServerOrder(
            $catalogService,
            (string) ($validatedData['SERIAL_NUMBER'] ?? ''),
            $quantity ?: null,
        );

        return $this->processarRespostaServer($response, $validatedData, $catalogService, $calculatedCost);
    }

    public function enviarOrdemAPIserver(Request $request, DhruOrderService $dhruOrderService)
    {
        return $this->enviarOrdemServer($request, $dhruOrderService);
    }

    public function getIMEIOrder(string $orderID)
    {
        $order = ImeiService::query()->where('referenceid', $orderID)->firstOrFail();

        return response()->json($order);
    }

    public function checkAndUpdateIMEIOrdersStatus()
    {
        return $this->server();
    }

    public function server()
    {
        $imeiOrders = ImeiService::whereIn('status', [0, 1, 2])->whereNotNull('dhru_provider_id')->get();
        $serverOrders = server_services::whereIn('status', [0, 1, 2])->whereNotNull('dhru_provider_id')->get();

        if ($imeiOrders->isEmpty() && $serverOrders->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Nenhuma ordem encontrada para atualizar.',
            ]);
        }

        foreach ($imeiOrders as $order) {
            ProcessImeiOrder::dispatch($order);
        }

        foreach ($serverOrders as $order) {
            ProcessServerOrder::dispatch($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordens colocadas na fila para processamento.',
        ]);
    }

    protected function processarRespostaImei(array $response, array $validatedData, DhruCatalogService $catalogService)
    {
        $this->saveResponseToFile($response, 'order_response.json');

        if (! isset($response['SUCCESS'][0]['REFERENCEID'])) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao enviar a ordem.',
                'details' => $response,
            ], 422);
        }

        $cost = (float) $catalogService->cost;
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        $user->credit -= $cost;
        $user->save();

        $referenceId = $response['SUCCESS'][0]['REFERENCEID'];

        // Corrige o problema de chaves duplicadas caso a API retorne 'VITRINE' (modo teste/mock)
        if ($referenceId === 'VITRINE') {
            $referenceId = 'VITRINE_' . uniqid();
        }

        $imeiOrder = ImeiService::create([
            'servicename' => $validatedData['servicename'],
            'serviceid' => $validatedData['SERVICEID'],
            'cost' => $cost,
            'referenceid' => $referenceId,
            'user_id' => $userId,
            'dhru_provider_id' => $catalogService->dhru_provider_id,
            'dhru_catalog_service_id' => $catalogService->id,
            'IMEI' => $validatedData['SERIAL_NUMBER'],
        ]);

        Log::info('Pedido IMEI salvo com provedor vinculado.', ['order_id' => $imeiOrder->id]);

        return response()->json([
            'success' => true,
            'message' => $response['SUCCESS'][0]['MESSAGE'] ?? 'Pedido enviado com sucesso.',
            'reference_id' => $response['SUCCESS'][0]['REFERENCEID'],
            'response' => $response,
        ]);
    }

    protected function processarRespostaServer(array $response, array $validatedData, DhruCatalogService $catalogService, float $cost)
    {
        $this->saveResponseToFile($response, 'order_response.json');

        if (! isset($response['SUCCESS'][0]['REFERENCEID'])) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao enviar a ordem.',
                'details' => $response,
            ], 422);
        }

        $userId = Auth::id();
        $user = User::findOrFail($userId);
        $user->credit -= $cost;
        $user->save();

        $referenceId = $response['SUCCESS'][0]['REFERENCEID'];

        // Corrige o problema de chaves duplicadas caso a API retorne 'VITRINE' (modo teste/mock)
        if ($referenceId === 'VITRINE') {
            $referenceId = 'VITRINE_' . uniqid();
        }

        $serverOrder = server_services::create([
            'servicename' => $validatedData['servicename'],
            'serviceid' => $validatedData['SERVICEID'],
            'cost' => $cost,
            'referenceid' => $referenceId,
            'user_id' => $userId,
            'dhru_provider_id' => $catalogService->dhru_provider_id,
            'dhru_catalog_service_id' => $catalogService->id,
            'IMEI' => (string) ($validatedData['SERIAL_NUMBER'] ?? ''),
            'Qnt' => $validatedData['Qnt'] ?? null,
        ]);

        Log::info('Pedido Server salvo com provedor vinculado.', ['order_id' => $serverOrder->id]);

        return response()->json([
            'success' => true,
            'message' => $response['SUCCESS'][0]['MESSAGE'] ?? 'Pedido enviado com sucesso.',
            'reference_id' => $response['SUCCESS'][0]['REFERENCEID'],
            'response' => $response,
        ]);
    }

    protected function resolveCatalogService(array $validatedData, string $expectedType): DhruCatalogService
    {
        return DhruCatalogService::query()
            ->with('provider')
            ->whereKey($validatedData['CATALOG_SERVICE_ID'])
            ->where('dhru_provider_id', $validatedData['PROVIDER_ID'])
            ->where('external_service_id', $validatedData['SERVICEID'])
            ->where('group_type', strtoupper($expectedType))
            ->where('is_active', true)
            ->firstOrFail();
    }

    protected function verificarCreditosSuficientes(float $cost): bool
    {
        $user = User::findOrFail(Auth::id());

        return $user->credit >= $cost;
    }

    protected function saveResponseToFile(array $response, string $fileName): void
    {
        Storage::put('responses/' . $fileName, json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
