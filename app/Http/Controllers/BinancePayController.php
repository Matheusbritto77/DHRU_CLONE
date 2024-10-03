<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Inclua o arquivo PHP com a função de pagamento


class BinancePayController extends Controller
{
    // Método para obter informações da conta spot na Binance
    public function getInfo()
    {
        // Defina suas credenciais da Binance
        $apiKey = '6ZseBUMKL22DISUfTIwRitToNLaKHY5CgOXh6K3uG1Bxcx4RsGZBl4EnJHFmM7jX';
        $apiSecret = 'zDIt4PhT2L6VG8Qtqzun6j9rOUgx6TQcPfy3DPWhzsJpHGHZ3685NeemlOe6rvpb';

        // URL da API da Binance para informações da conta spot
        $url = 'https://api.binance.com/binancepay/openapi/v3/order';

        // Gerar o timestamp atual em milissegundos
        $timestamp = round(microtime(true) * 1000);

        // Parâmetros da requisição
        $params = [
            'timestamp' => $timestamp,
        ];

        // Construir a string para assinatura HMAC SHA256
        $query_string = http_build_query($params);
        $signature = hash_hmac('sha256', $query_string, $apiSecret);

        // Configuração do cliente Guzzle
        $client = new Client([
            'base_uri' => $url,
            'timeout' => 10.0,
            'headers' => [
                'X-MBX-APIKEY' => $apiKey,
            ],
        ]);

        try {
            // Fazer a requisição GET para obter informações da conta
            $response = $client->request('GET', '', [
                'query' => array_merge($params, ['signature' => $signature]),
            ]);

            // Verificar se a requisição foi bem-sucedida
            if ($response->getStatusCode() == 200) {
                $account_info = json_decode($response->getBody(), true);

                // Verificar se 'balances' existe e é uma lista
                if (isset($account_info['balances']) && is_array($account_info['balances'])) {
                    // Filtrar os saldos para exibir apenas os saldos positivos na carteira spot
                    $balances = array_filter($account_info['balances'], function ($asset) {
                        return isset($asset['free']) && floatval($asset['free']) > 0;
                    });

                    // Formatando os saldos para um array associativo
                    $formatted_balances = [];
                    foreach ($balances as $asset) {
                        $formatted_balances[$asset['asset']] = floatval($asset['free']);
                    }

                    // Retornar os saldos como JSON
                    return response()->json($formatted_balances);
                } else {
                    // Retornar mensagem de erro se não puder obter os saldos
                    return response()->json(['error' => 'Não foi possível obter os saldos da carteira spot na Binance.']);
                }
            } else {
                // Retornar mensagem de erro se a requisição falhou
                return response()->json(['error' => 'Erro na requisição para obter informações da conta na Binance.'], $response->getStatusCode());
            }
        } catch (RequestException $e) {
            // Retornar mensagem de erro se ocorrer uma exceção de requisição
            return response()->json(['error' => 'Erro na requisição para obter informações da conta na Binance: ' . $e->getMessage()], 500);
        }
    }




























    public function createBinanceOrder()
    {// Credenciais da Binance
        $api_key = 'ka48wndprrdjwm4e2zna3i0pp7gdv9xynnnkvr5xsvfhthacayzi3y9rpvimzwyg';
        $api_secret = '8T7VLZKBPZPZPLKYIC0ncblszrwtdvdxqiu8blk8fjkspes7kx52l2kcz1zbjqyb';

        // Dados do pedido
        $orderData = [
            'env' => [
                'terminalType' => 'APP',
            ],
            'orderTags' => [
                'ifProfitSharing' => true,
            ],
            'merchantTradeNo' => uniqid(), // Gerar um ID único para o pedido
            'orderAmount' => 10.0, // Montante do pedido em USDT
            'currency' => 'USDT',
            'description' => 'Pagamento de teste',
            'goodsDetails' => [
                [
                    'goodsType' => '01',
                    'goodsCategory' => 'D000',
                    'referenceGoodsId' => '123456789',
                    'goodsName' => 'Teste Produto',
                    'goodsDetail' => 'Descrição do produto de teste',
                ],
            ],
        ];

        // Timestamp atual em milissegundos
        $timestamp = Carbon::now()->timestamp * 1000;

        // Montar a assinatura da requisição
        $signature = hash_hmac(
            'sha256',
            http_build_query($orderData) . $timestamp,
            $api_secret
        );

        // Adicionar o timestamp e a assinatura aos dados do pedido
        $orderData['timestamp'] = $timestamp;
        $orderData['signature'] = $signature;

        // Endpoint da API da Binance para criar um pedido
        $endpoint = 'https://api.binance.com/binancepay/openapi/v3/order';

        // Enviar a requisição para criar um pedido
        $response = Http::withHeaders([
            'X-MBX-APIKEY' => $api_key,
        ])->post($endpoint, $orderData);

        // Verificar se a requisição foi bem-sucedida
        if ($response->successful()) {
            $responseData = $response->json();

            // Verificar se o pedido foi criado com sucesso
            if (isset($responseData['response']['checkoutUrl'])) {
                // Redirecionar o usuário para o checkoutUrl da Binance
                return redirect()->away($responseData['response']['checkoutUrl']);
            } else {
                // Lidar com erros caso o checkoutUrl não seja retornado
                return back()->withError('Erro ao criar pedido na Binance.');
            }
        } else {
            // Lidar com erros de requisição para a API da Binance
            return back()->withError('Erro na requisição para a API da Binance.');
        }
    }
}