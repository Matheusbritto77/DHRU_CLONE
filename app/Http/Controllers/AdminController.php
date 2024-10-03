<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\ImeiService;
use App\Models\server_services;
use App\Models\User;



class AdminController extends Controller

{
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
   /**
     * Retorna a view do dashboard administrativo com o total de usuários registrados.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        $totalUsuarios = $this->totalUsuariosRegistrados(); // Chamando a função que retorna o total de usuários registrados

        return view('admin.dashboard', compact('totalUsuarios'));
    }

    /**
     * Retorna o total de usuários registrados na tabela users.
     *
     * @return int
     */
    private function totalUsuariosRegistrados()
    {
        return User::count();
    }

   public function updateCostPercentage(Request $request)
{
    // Validação dos dados recebidos
    $request->validate([
        'cost_percentage' => 'required|numeric|min:0',
    ]);

    // Caminho do arquivo JSON
    $filePath = public_path('list/list.json');

    try {
        // Verifica se o arquivo existe
        if (!File::exists($filePath)) {
            throw new \Exception('Arquivo JSON não encontrado');
        }

        // Lê o conteúdo do arquivo JSON
        $jsonContents = File::get($filePath);

        // Decodifica o JSON para array associativo
        $services = json_decode($jsonContents, true);

        // Obtém a porcentagem de aumento do custo
        $costPercentage = $request->cost_percentage;

        // Percorre os serviços e atualiza o campo 'CREDIT' para cada serviço
foreach ($services['SUCCESS'] as &$serviceGroup) {
    foreach ($serviceGroup['LIST'] as &$service) {
        // Verifica se o campo 'SERVICES' está definido e é um array
        if (isset($service['SERVICES']) && is_array($service['SERVICES'])) {
            foreach ($service['SERVICES'] as &$individualService) {
                // Verifica se o campo 'CREDIT' está definido e é numérico
                if (isset($individualService['CREDIT']) && is_numeric($individualService['CREDIT'])) {
                    // Converte para float para manter todas as casas decimais originais
                    $currentCredit = (float) $individualService['CREDIT'];

                    // Calcula o novo valor de 'CREDIT' com o aumento percentual
                    $newCredit = $currentCredit * (1 + ($costPercentage / 100));

                    // Se após o aumento percentual o valor for zero ou muito próximo, ajusta para 0.010
                    if (round($newCredit, 3) <= 1.000) {
                        $newCredit = 1.10;
                    } else {
                        // Limita o número de casas decimais para no máximo 4, mas não deixa crescer além disso
                        $newCredit = round($newCredit, 4);
                    }

                    // Converte para string mantendo a precisão original
                    $individualService['CREDIT'] = (string) $newCredit;
                }
            }
        }
    }
}


        // Codifica de volta para JSON
        $updatedJson = json_encode($services, JSON_PRETTY_PRINT);

        // Salva o JSON de volta no arquivo
        File::put($filePath, $updatedJson);

        // Log: Arquivo JSON atualizado com sucesso
        Log::info('Arquivo JSON atualizado com sucesso');

        return redirect()->back()->with('success', 'Porcentagem de custo atualizada com sucesso!');
    } catch (\Exception $e) {
        // Log: Erro ao atualizar porcentagem de custo
        Log::error('Erro ao atualizar porcentagem de custo: ' . $e->getMessage());

        return redirect()->back()->with('error', 'Erro ao atualizar porcentagem de custo: ' . $e->getMessage());
    }
}


























/**
 * Exibe os serviços de IMEI na view imei.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
 */
public function showIMEIServices(Request $request)
{
    $filePath = public_path('list/list.json');

    if (!File::exists($filePath)) {
        return response()->json(['error' => 'Arquivo de serviços não encontrado.'], 404);
    }

    $fileContents = File::get($filePath);
    $json = json_decode($fileContents, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return response()->json(['error' => 'Erro ao decodificar o arquivo JSON.'], 500);
    }

    $services = $json['SUCCESS'];

    $filteredServices = collect($services)
    ->flatMap(function ($serviceGroup) {
        return collect($serviceGroup['LIST'])
            ->filter(function ($group) {
                return ($group['GROUPTYPE'] === 'SERVER' || $group['GROUPTYPE'] === 'IMEI') && isset($group['SERVICES']);
            })
            ->flatMap(function ($group) {
                return collect($group['SERVICES'])
                        ->map(function ($service) use ($group) {
                            // Filtrar apenas os campos desejados e incluir o nome do grupo
                            $customFieldnames = collect($service['Requires.Custom'] ?? [])
                                ->pluck('fieldname')
                                ->implode(', ');

                            return [
                                'GROUPNAME' => $group['GROUPNAME'],
                                'SERVICENAME' => $service['SERVICENAME'],
                                'TIME' => $service['TIME'],
                                'CREDIT' => $service['CREDIT'],
                                'MINQNT' => $service['MINQNT'] ?? '',
                                'SERVICEID' => $service['SERVICEID'] ?? '',
                                'customname' => $service['CUSTOM']['customname'] ?? '',
                                'fieldname' => $customFieldnames,
                            ];
                        })
                        ->toArray();
                })
                ->toArray();
        })
        ->toArray();

     return view('admin.imei', compact('filteredServices'));
}

















/**
     * Atualiza os campos de um serviço específico na lista JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateServiceFields(Request $request)

{ // Validação dos dados recebidos
    $request->validate([
        'serviceID' => 'required|integer',
        'serviceName' => 'required|string',
        'time' => 'required|string',
        'cost' => 'required|numeric',
        'customFields' => 'nullable|string',
    ]);

    // Caminho do arquivo JSON
    $filePath = public_path('list/list.json');

    try {
        // Verifica se o arquivo existe
        if (!File::exists($filePath)) {
            throw new \Exception('Arquivo JSON não encontrado');
        }

        // Lê o conteúdo do arquivo JSON
        $jsonContents = File::get($filePath);

        // Decodifica o JSON para array associativo
        $services = json_decode($jsonContents, true);

        // Verifica se o JSON foi decodificado corretamente
        if ($services === null || !isset($services['SUCCESS']) || !is_array($services['SUCCESS'])) {
            throw new \Exception('Erro ao decodificar o arquivo JSON');
        }

        // Encontra o serviço pelo ID e atualiza os dados
        $serviceFound = false;
        foreach ($services['SUCCESS'] as &$serviceGroup) {
            foreach ($serviceGroup['LIST'] as &$service) {
                // Verifica se o campo 'SERVICES' está definido e é um array
                if (isset($service['SERVICES']) && is_array($service['SERVICES'])) {
                    foreach ($service['SERVICES'] as &$individualService) {
                        if ($individualService['SERVICEID'] == $request->serviceID) {
                            $individualService['SERVICENAME'] = $request->serviceName;
                            $individualService['TIME'] = $request->time;
                            $individualService['CREDIT'] = $request->cost;
                            if ($request->customFields) {
                                $individualService['customname'] = $request->customFields;
                            } else {
                                unset($individualService['customname']);
                            }
                            $serviceFound = true;
                            break 3; // Sai dos três loops aninhados
                        }
                    }
                }
            }
        }

        // Verifica se o serviço foi encontrado e atualizado
        if (!$serviceFound) {
            throw new \Exception('Serviço não encontrado');
        }

        // Codifica o array de volta para JSON
        $newJsonContents = json_encode($services, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Salva as alterações no arquivo JSON
        File::put($filePath, $newJsonContents);

        // Retorna uma resposta de sucesso
        return response()->json(['success' => true, 'message' => 'Serviço atualizado com sucesso']);
    } catch (\Exception $e) {
        // Retorna uma resposta de erro
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
}














/**
     * Mostra o histórico de IMEI para o administrador.
     *
     * @return \Illuminate\View\View
     */
    public function showIMEIHistory()
    {
        try {
            // Recupera todas as ordens de IMEI
            $imeiOrders = ImeiService::orderByDesc('created_at')->get();
            Log::info('Ordens de IMEI recuperadas:', ['imeiOrders' => $imeiOrders]);

            // Recupera todas as ordens de server_services
            $serverOrders = server_services::orderByDesc('created_at')->get();
            Log::info('Ordens de server_services recuperadas:', ['serverOrders' => $serverOrders]);

            // Junta as ordens em uma coleção e ordena por data de criação decrescente
            $allOrders = $imeiOrders->merge($serverOrders)->sortByDesc('created_at')->values();

            // Filtra apenas os campos necessários
            $filteredOrders = $allOrders->map(function($order) {
                return [
                    'status' => $order->status,
                    'code' => $order->code,
                    'IMEI' => $order->IMEI,
                    'referenceid' => $order->referenceid,
                    'cost' => $order->cost,
                    'user_id' => $order->user_id,
                    'servicename' => $order->servicename,
                    'Qnt' => $order->Qnt,
                ];
            });

            // Retorna a visão com os dados das ordens
            return view('admin.history', ['orders' => $filteredOrders]);

        } catch (\Exception $e) {
            Log::error('Erro ao recuperar histórico de IMEI:', ['exception' => $e]);
            return back()->withError('Erro ao recuperar histórico de IMEI. Por favor, tente novamente mais tarde.');
        }
    }
























}