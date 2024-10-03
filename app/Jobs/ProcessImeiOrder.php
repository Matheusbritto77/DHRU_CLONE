<?php

namespace App\Jobs;

use App\Models\ImeiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use App\Models\User;

class ProcessImeiOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imeiOrder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ImeiService $imeiOrder)
    {
        $this->imeiOrder = $imeiOrder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $referenceId = $this->imeiOrder->referenceid;

        // Configuração das credenciais da API Dhru Fusion
        $apiUrl = env('DHRU_API_URL');
        $username = env('DHRU_API_USERNAME');
        $apiKey = env('DHRU_API_APIKEY');

        // Prepara os parâmetros para a chamada à API getimeiorder
        $params = [
            'username' => $username,
            'apiaccesskey' => $apiKey,
            'requestformat' => 'JSON',
            'action' => 'getimeiorderbulk', // Ação para obter status de uma ordem
            'parameters' => base64_encode(json_encode([['ID' => $referenceId]])) // Encode para base64 conforme padrão API
        ];

        // Log dos parâmetros formados antes da chamada à API
        Log::info('Parâmetros formados para chamada à API', ['params' => $params]);

        // Monta a URL da API para obter o status da ordem de IMEI
        $url = $apiUrl . 'index.php';

        // Realiza a chamada à API Dhru Fusion usando cURL
        $response = $this->callAPI($url, $params);

        // Verifica se houve resposta da API e se a resposta está correta
        if (!is_array($response) || empty($response)) {
            Log::error('Resposta inválida ou ausente da API', ['response' => $response]);
            return;
        }

        // Verifique se a resposta da API é decodificada corretamente
        Log::info('Resposta da API decodificada', ['responseData' => $response]);

        // Processa e atualiza a ordem de IMEI com base na resposta da API
        $this->processAndUpdateImeiOrder($response, $referenceId);
    }

    /**
     * Realiza a chamada à API Dhru Fusion usando cURL.
     *
     * @param string $url A URL da API.
     * @param array $params Os parâmetros para a chamada à API.
     * @return array A resposta da API.
     */
    private function callAPI($url, $params)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Processa a resposta da API e atualiza a tabela imei_services.
     *
     * @param array $response A resposta da API como array associativo.
     * @param int $referenceId O ID de referência da ordem.
     * @return void
     */
    private function processAndUpdateImeiOrder(array $response, $referenceId)
    {
        // Itera sobre a resposta para encontrar o campo SUCCESS
        foreach ($response as $key => $value) {
            if (is_array($value) && isset($value['SUCCESS'])) {
                foreach ($value['SUCCESS'] as $order) {
                    // Extrai os campos 'IMEI', 'STATUS' e 'CODE'
                    $imei = $order['IMEI'];
                    $status = $order['STATUS'];
                    $code = $order['CODE'];

                    // Encontra a ordem na tabela imei_services pelo referenceId
                    $imeiOrder = ImeiService::where('referenceid', $referenceId)->first();

                    // Verifica se a ordem foi encontrada
                    if ($imeiOrder) {
                        // Log para verificar o servicename
                        Log::debug('Verificando servicename', [
                            'ReferenceID' => $referenceId,
                            'servicename' => $imeiOrder->servicename,
                        ]);

                        // Verifica se o status atual é diferente do status na resposta da API
                        if ($imeiOrder->status != $status) {
                            // Verifica se o novo status é 3
                            if ($status == 3) {
                                // Verifica se o servicename contém a frase "No Refund"
                                if (stripos($imeiOrder->servicename, 'No Refund') !== false) {
                                    // Atualiza apenas o status para 3 (sem reembolso)
                                    $imeiOrder->update([
                                        'status' => $status,
                                        'code' => $code,
                                    ]);

                                    // Log da atualização sem reembolso
                                    Log::info('Ordem de IMEI atualizada para status 3 (sem reembolso)', [
                                        'ReferenceID' => $referenceId,
                                        'IMEI' => $imei,
                                        'status_antigo' => $imeiOrder->status,
                                        'status_novo' => $status,
                                        'code' => $code,
                                    ]);
                                } else {
                                    // Atualiza o status para 3 e processa o reembolso
                                    $this->processRefundImei($imeiOrder, $code);

                                    // Log da atualização com reembolso
                                    Log::info('Ordem de IMEI atualizada para status 3 (com reembolso)', [
                                        'ReferenceID' => $referenceId,
                                        'IMEI' => $imei,
                                        'status_antigo' => $imeiOrder->status,
                                        'status_novo' => $status,
                                        'code' => $code,
                                    ]);
                                }
                            } else {
                                // Atualiza o status e o código da ordem na tabela
                                $imeiOrder->update([
                                    'status' => $status,
                                    'code' => $code,
                                ]);

                                // Log da atualização de status normal
                                Log::info('Ordem de IMEI atualizada', [
                                    'ReferenceID' => $referenceId,
                                    'IMEI' => $imei,
                                    'status_antigo' => $imeiOrder->status,
                                    'status_novo' => $status,
                                    'code' => $code,
                                ]);
                            }
                        }
                    } else {
                        // Log se a ordem não for encontrada
                        Log::warning('Ordem de IMEI não encontrada para atualização', [
                            'ReferenceID' => $referenceId,
                            'IMEI' => $imei,
                            'status' => $status,
                            'code' => $code,
                        ]);
                    }
                }

                return;
            }
        }

        // Log se não houver campo 'SUCCESS' na resposta
        Log::error('Resposta da API não contém campo SUCCESS', ['response' => $response]);
    }

    /**
     * Processa o reembolso adicionando o valor de cost ao crédito do usuário.
     *
     * @param ImeiService $imeiOrder A ordem de serviço de IMEI para a qual o reembolso está sendo processado.
     * @param string $code O código associado ao reembolso.
     * @return void
     */
    private function processRefundImei(ImeiService $imeiOrder, $code)
    {
        // Obter o valor de cost registrado para este serviço
        $cost = $imeiOrder->cost;

        // Obter o ID do usuário associado ao serviço de IMEI
        $userId = $imeiOrder->user_id;

        // Buscar o usuário pelo ID associado ao serviço de IMEI
        $user = User::find($userId);

        // Verificar se o usuário foi encontrado
        if ($user) {
            // Adicionar o valor de cost ao crédito do usuário
            $user->credit += $cost;
            $user->save();

            // Atualizar o status e o código da ordem na tabela
            $imeiOrder->update([
                'status' => 3,
                'code' => $code,
            ]);

            // Log da atualização com reembolso
            Log::info('Ordem de IMEI atualizada para status 3 (com reembolso)', [
                'ReferenceID' => $imeiOrder->referenceid,
                'IMEI' => $imeiOrder->imei,
                'status_antigo' => $imeiOrder->status,
                'status_novo' => 3,
                'code' => $code,
            ]);
        } else {
            // Se o usuário não for encontrado, registrar um aviso ou erro
            Log::error('Não foi possível encontrar o usuário associado à ordem de IMEI para processar o reembolso.', [
                'ReferenceID' => $imeiOrder->referenceid,
                'IMEI' => $imeiOrder->imei,
            ]);
        }
    }
}
