<?php

namespace App\Http\Controllers;

use App\Models\server_services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
use App\Models\ImeiService;

class ServerController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    /**
 * Exibe os serviços de SERVER na view server.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\JsonResponse
 */
public function showServerServices(Request $request)
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
                        return $group['GROUPTYPE'] === 'SERVER' && isset($group['SERVICES']);
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
                                'MAXQNT' => $service['MAXQNT'] ?? '',
                                'SERVICEID' => $service['SERVICEID'] ?? '',
                                'fieldname' => $customFieldnames,
                            ];
                        })
                        ->toArray();
                })
                ->toArray();
        })
        ->toArray();

    // Retorna os dados filtrados como JSON
    return view('Server', compact('filteredServices'));
}


    /**
 * Mostra o histórico de IMEI para o usuário logado.
 *
 * @return \Illuminate\View\View
 */
public function showIMEIHistory()
{
    try {
        // Obtém o ID do usuário logado
        $userId = Auth::id();
        Log::info('ID do usuário logado:', ['userId' => $userId]);

        // Busca as ordens de IMEI relacionadas ao usuário logado
        $imeiOrders = server_services::where('user_id', $userId)
            ->orderByDesc('created_at') // Ordena por data de criação decrescente
            ->paginate(10); // Paginação com 10 registros por página

        // Verifica se as ordens foram recuperadas corretamente
        Log::info('Ordens de IMEI recuperadas:', ['imeiOrders' => $imeiOrders]);

        // Retorna a visão com os dados das ordens
        return view('server_history', compact('imeiOrders'));

    } catch (\Exception $e) {
        Log::error('Erro ao recuperar histórico de IMEI:', ['exception' => $e]);
        return back()->withError('Erro ao recuperar histórico de IMEI. Por favor, tente novamente mais tarde.');
    }
}
}