<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ImeiService;
use App\Models\server_services; 
use Illuminate\Http\JsonResponse;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\ProcessServerOrder;
use App\Jobs\ProcessImeiOrder;
use GuzzleHttp\Client;
   




class ApiController extends Controller
{
    
    /**
     * Exibe o formulário de configuração da API.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function configurarApiForm()
    {
        return view('api.configurar_api'); // Verifique se 'configurar_api.blade.php' existe em resources/views/api
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
  /**
     * Obtém todos os serviços da API Dhru e salva em um arquivo JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllServices()
    {
        Log::info('Iniciando obtenção de todos os serviços da API Dhru.');

        // Busca as credenciais do .env
        $apiUrl = env('DHRU_API_URL');
        $username = env('DHRU_API_USERNAME');
        $apiKey = env('DHRU_API_APIKEY');

        // Cliente HTTP Guzzle
        $client = new Client();

        try {
            // Faz a requisição para a API Dhru
            $response = $client->post($apiUrl, [
                'form_params' => [
                    'username' => $username,
                    'apiaccesskey' => $apiKey,
                    'requestformat' => 'JSON',
                    'action' => 'imeiservicelist'
                ]
            ]);

            // Converte a resposta para um array
            $services = json_decode($response->getBody()->getContents(), true);

            Log::info('Resposta da API Dhru obtida', ['response' => $services]);

            // Salva os serviços em um arquivo JSON na pasta 'responses'
            $this->saveResponseToJson($services, 'services.json');

            return response()->json(['message' => 'Serviços salvos com sucesso!'], 200);
            
        } catch (\Exception $e) {
            // Trata qualquer exceção
            Log::error('Ocorreu um erro ao se comunicar com a API Dhru', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Ocorreu um erro ao se comunicar com a API Dhru: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Função para salvar a resposta da API em um arquivo JSON.
     *
     * @param array $response
     * @param string $fileName
     */
    private function saveResponseToJson($response, $fileName)
    {
        $filePath = 'responses/' . $fileName;
        Storage::put($filePath, json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Log::info('Resposta salva em arquivo JSON', ['filePath' => $filePath]);
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

/**
 * Função para enviar uma ordem única para a API usando o "Serial Number" ou "SN".
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function enviarOrdemSerialNumber(Request $request)
{
    Log::info('Iniciando envio de ordem com Serial Number', ['request' => $request->all()]);

    // Valida os dados do formulário manualmente
    $validatedData = $request->validate([
        'SERIAL_NUMBER' => '',
        'SERVICEID' => '',
        'COST' => '', // Adicionar validação para o campo COST
        'servicename' => '',
    ]);

    if (!$validatedData) {
        Log::error('Erro na validação dos dados', ['validatedData' => $validatedData]);
        return response()->json(['error' => 'Erro na validação dos dados.']);
    }

    // Verificar créditos suficientes em relação ao COST do serviço
    if (!$this->verificarCreditosSuficientes($validatedData['COST'])) {
        Log::error('Créditos insuficientes para realizar a ordem');
        return response()->json(['error' => 'Créditos insuficientes para realizar a ordem.']);
    }

    // Faz a chamada à API e obtém a resposta
    $response = $this->enviarOrdemAPI($validatedData);

    // Processa a resposta da API e salva os dados
    return $this->processarRespostaAPI($response, $validatedData);
}

/**
 * Função para enviar a ordem para a API e obter a resposta.
 *
 * @param array $validatedData
 * @return array
 */
private function enviarOrdemAPI(array $validatedData)
{
    // Busca as credenciais do .env
    $apiUrl = env('DHRU_API_URL');
    $username = env('DHRU_API_USERNAME');
    $apiKey = env('DHRU_API_APIKEY');

    // Define a ação da API
    $action = 'placeimeiorder';

    // Constrói os parâmetros da requisição
$params = [
    'username' => $username,
    'apiaccesskey' => $apiKey,
    'requestformat' => 'JSON',
    'action' => $action,
    'parameters' => '<PARAMETERS>' . 
                    '<IMEI>' . $validatedData['SERIAL_NUMBER'] . '</IMEI>' . 
                    '<ID>' . $validatedData['SERVICEID'] . '</ID>' . 
                    '<CUSTOMFIELD>' . base64_encode(json_encode([
                        'SERIAL_NUMBER' => $validatedData['SERIAL_NUMBER'],
                        'SN' => $validatedData['SERIAL_NUMBER'], // Defina SN como SERIAL_NUMBER
                    ])) . '</CUSTOMFIELD>' . 
                    '</PARAMETERS>'
];


    Log::info('Parâmetros da requisição', ['params' => $params]);

    // Faz a chamada à API usando cURL
    return $this->callAPI($apiUrl . 'index.php', $params);
}

/**
 * Função para processar a resposta da API e salvar os dados necessários.
 *
 * @param array $response
 * @param array $validatedData
 * @return \Illuminate\Http\JsonResponse
 */
private function processarRespostaAPI(array $response, array $validatedData)
{
    Log::info('Resposta da API para envio de ordem', ['response' => $response]);

    // Salva a resposta em um arquivo JSON
    $this->saveResponseToFile($response, 'order_response.json');

    // Verifica se a chamada foi bem-sucedida
    if (isset($response['SUCCESS'])) {
        // Subtrai o COST dos créditos do usuário
        $cost = $validatedData['COST'];
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        $user->credit -= $cost;
        $user->save();

        // Armazena os dados na tabela imei_services
        try {
            $this->armazenarDadosIMEIService($validatedData, $response, $cost, $userId);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar dados na tabela imei_services', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erro ao salvar dados na tabela imei_services.']);
        }

        // Retorna uma resposta de sucesso com a mensagem e o ID de referência
        return response()->json([
            'success' => true,
            'message' => $response['SUCCESS'][0]['MESSAGE'],
            'reference_id' => $response['SUCCESS'][0]['REFERENCEID'],
            'response' => $response
        ]);
    } else {
        // Retorna uma resposta de erro com detalhes
        return response()->json([
            'success' => false,
            'error' => 'Erro ao enviar a ordem.',
            'details' => $response
        ]);
    }
}

/**
 * Função para armazenar os dados na tabela imei_services.
 *
 * @param array $validatedData
 * @param array $response
 * @param float $cost
 * @param int $userId
 * @throws \Exception
 */
private function armazenarDadosIMEIService(array $validatedData, array $response, float $cost, int $userId)
{
    $imeiService = new ImeiService();
    $imeiService->servicename = $validatedData['servicename']; // Captura o servicename do request
    $imeiService->serviceid = $validatedData['SERVICEID'];
    $imeiService->cost = $cost;
    $imeiService->referenceid = $response['SUCCESS'][0]['REFERENCEID'];
    $imeiService->user_id = $userId;
    $imeiService->IMEI = $validatedData['SERIAL_NUMBER'];
    $imeiService->save();

    Log::info('Dados salvos com sucesso na tabela imei_services', ['imeiService' => $imeiService]);
}

    

// Função para verificar se o usuário tem créditos suficientes em relação ao COST do serviço
private function verificarCreditosSuficientes($cost)
{
    $userId = Auth::id();
    $user = User::findOrFail($userId);

    // Verifica se o usuário tem créditos suficientes em relação ao COST do serviço
    return $user->credit >= $cost;
}

/**
 * Função para chamar a API usando cURL.
 *
 * @param string $url
 * @param array $params
 * @return array
 */
private function callAPI($url, $params)
{
    Log::info('Iniciando chamada à API', ['url' => $url, 'params' => $params]);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    Log::info('Resposta cURL', ['httpCode' => $httpCode, 'response' => $response]);

    if ($response === false) {
        $error = ['error' => 'Erro na solicitação cURL: ' . curl_error($ch)];
        curl_close($ch);
        Log::error('Erro na solicitação cURL', ['error' => $error]);
        return $error;
    }

    // Verifica se a resposta é válida JSON
    $responseData = json_decode($response, true);
    if ($responseData === null && json_last_error() !== JSON_ERROR_NONE) {
        $error = [
            'error' => 'Erro ao decodificar a resposta JSON.',
            'raw_response' => $response,
            'http_code' => $httpCode
        ];
        Log::error('Erro ao decodificar a resposta JSON', ['error' => $error]);
        return $error;
    }

    // Adiciona informações adicionais à resposta para depuração
    $responseData['http_code'] = $httpCode;
    $responseData['raw_response'] = $response;

    Log::info('Resposta da API decodificada', ['responseData' => $responseData]);

    curl_close($ch);

    return $responseData;
}

/**
 * Função para salvar a resposta da API em um arquivo JSON.
 *
 * @param array $response
 * @param string $fileName
 */
private function saveResponseToFile($response, $fileName)
{
    $filePath = 'responses/' . $fileName;
    Storage::put($filePath, json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

















































































public function server()
{
    
    
    
        // Busca todos os IDs de referência das ordens com status 0, 1 ou 2 de ambas as tabelas
        $imeiOrders = ImeiService::whereIn('status', [0, 1, 2])->get();
        $serverOrders = server_services::whereIn('status', [0, 1, 2])->get();
    
        // Verifica se há IDs para processar
        if ($imeiOrders->isEmpty() && $serverOrders->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Nenhuma ordem encontrada para atualizar.'
            ]);
        }
    
        // Coloca as ordens IMEI na fila
        foreach ($imeiOrders as $order) {
            ProcessImeiOrder::dispatch($order);
        }
    
        // Coloca as ordens Server na fila
        foreach ($serverOrders as $order) {
            ProcessServerOrder::dispatch($order);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Ordens colocadas na fila para processamento.'
        ]);
    }
    








































/**
 * Função para enviar uma ordem única para a API usando o "Serial Number" ou "SN".
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function enviarOrdemServer(Request $request)
{
    Log::info('Iniciando envio de ordem com Serial Number', ['request' => $request->all()]);

    // Valida os dados do formulário manualmente
    $validatedData = $request->validate([
        'SERVICEID' => '',
        'COST' => '',
        'servicename' => '',
        'username' => '',
        'Qnt' => '',
        'SERIAL_NUMBER' => '', // Validar SERIAL_NUMBER como uma string simples
    ]);

    if (!$validatedData) {
        Log::error('Erro na validação dos dados', ['validatedData' => $validatedData]);
        return response()->json(['error' => 'Erro na validação dos dados.']);
    }

    // Não é necessário fazer mais nenhuma manipulação se SERIAL_NUMBER é apenas uma string simples

    // Verificar créditos suficientes em relação ao COST do serviço
    if (!$this->verificarCreditosSuficientes($validatedData['COST'])) {
        Log::error('Créditos insuficientes para realizar a ordem');
        return response()->json(['error' => 'Créditos insuficientes para realizar a ordem.']);
    }

    // Faz a chamada à API e obtém a resposta
    $response = $this->enviarOrdemAPIserver($validatedData);

    // Processa a resposta da API e salva os dados
    return $this->processarRespostaAPIserver($response, $validatedData);
}

/**
 * Função para enviar a ordem para a API e obter a resposta.
 *
 * @param array $validatedData
 * @return array
 */
private function enviarOrdemAPIserver(array $validatedData)
{
    // Busca as credenciais do .env
    $apiUrl = env('DHRU_API_URL');
    $username = env('DHRU_API_USERNAME');
    $apiKey = env('DHRU_API_APIKEY');

    // Define a ação da API
    $action = 'placeimeiorder';

   

    // Extrair e decodificar SERIAL_NUMBER
    $serialNumberString = $validatedData['SERIAL_NUMBER'];
    $serialNumberArray = [];
    // Remover as aspas extras do começo e do fim
    $serialNumberString = trim($serialNumberString, '"');
    // Separar as partes do serial number
    $serialNumberParts = explode('","', $serialNumberString);
    foreach ($serialNumberParts as $part) {
        $keyValue = explode('":"', $part);
        if (count($keyValue) === 2) {
            $serialNumberArray[$keyValue[0]] = $keyValue[1];
        }
    }

    // Constrói os parâmetros da requisição
    $params = [
        'username' => $username,
        'apiaccesskey' => $apiKey,
        'requestformat' => 'JSON',
        'action' => $action,
        'parameters' => '<PARAMETERS>' . 
                        '<IMEI></IMEI>' . 
                        '<Qnt>' . $validatedData['Qnt'] . '</Qnt>' . 
                        '<ID>' . $validatedData['SERVICEID'] . '</ID>' . 
                        '<CUSTOMFIELD>' . base64_encode(json_encode($serialNumberArray)) . '</CUSTOMFIELD>' . 
                        '</PARAMETERS>'
    ];

    Log::info('Parâmetros da requisição', ['params' => $params]);

    // Faz a chamada à API usando cURL
    return $this->callAPI($apiUrl . 'index.php', $params);
}



/**
 * Função para processar a resposta da API e salvar os dados necessários.
 *
 * @param array $response
 * @param array $validatedData
 * @return \Illuminate\Http\JsonResponse
 */
private function processarRespostaAPIserver(array $response, array $validatedData)
{
    Log::info('Resposta da API para envio de ordem', ['response' => $response]);

    // Salva a resposta em um arquivo JSON
    $this->saveResponseToFile($response, 'order_response.json');

    // Verifica se a chamada foi bem-sucedida
    if (isset($response['SUCCESS'])) {
        // Subtrai o COST dos créditos do usuário
        $cost = $validatedData['COST'];
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        $user->credit -= $cost;
        $user->save();

        // Armazena os dados na tabela imei_services
        try {
            $this->armazenarDadosSrver($validatedData, $response, $cost, $userId);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar dados na tabela imei_services', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erro ao salvar dados na tabela imei_services.']);
        }

        // Retorna uma resposta de sucesso com a mensagem e o ID de referência
        return response()->json([
            'success' => true,
            'message' => $response['SUCCESS'][0]['MESSAGE'],
            'reference_id' => $response['SUCCESS'][0]['REFERENCEID'],
            'response' => $response
        ]);
    } else {
        // Retorna uma resposta de erro com detalhes
        return response()->json([
            'success' => false,
            'error' => 'Erro ao enviar a ordem.',
            'details' => $response
        ]);
    }
}


/**
 * Função para armazenar os dados na tabela imei_services.
 *
 * @param array $validatedData
 * @param array $response
 * @param float $cost
 * @param int $userId
 * @throws \Exception
 */
private function armazenarDadosSrver(array $validatedData, array $response, float $cost, int $userId)
{
    $server_services = new server_services();
    $server_services->servicename = $validatedData['servicename']; // Captura o servicename do request
    $server_services->serviceid = $validatedData['SERVICEID'];
    $server_services->cost = $cost;
    $server_services->referenceid = $response['SUCCESS'][0]['REFERENCEID'];
    $server_services->user_id = $userId;
    $server_services->IMEI = $validatedData['SERIAL_NUMBER'];
    $server_services->Qnt = $validatedData['Qnt'];
    $server_services->save();

    Log::info('Dados salvos com sucesso na tabela imei_services', ['server_services' => $server_services]);
}

    



 
}