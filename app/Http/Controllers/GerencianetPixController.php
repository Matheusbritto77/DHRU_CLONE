<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gerencianet\Gerencianet;
use Gerencianet\Exception\GerencianetException;
use Exception;
use App\Models\Deposito; // Importe o modelo Deposito aqui
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GerencianetPixController extends Controller
{
    public function generateQRCode(Request $request)
{
    $mode = config('gerencianet.mode');
    $certificate = config("gerencianet.{$mode}.certificate_name");

    $options = [
        'client_id' => config("gerencianet.{$mode}.client_id"),
        'client_secret' => config("gerencianet.{$mode}.client_secret"),
        'certificate' => base_path("certs/{$certificate}"),
        'sandbox' => $mode === 'sandbox',
        'debug' => config('gerencianet.debug'),
        'timeout' => 30,
    ];

    $totalBrl = $request->input('total_brl');
    $amountUsd = $request->input('amount_usd_hidden');

    $body = [
        'calendario' => [
            'expiracao' => 3600,
        ],
        'valor' => [
            'original' => $totalBrl,
        ],
        'chave' => config('gerencianet.default_key_pix'),
        'solicitacaoPagador' => 'Pagamento Plataforma XPTO',
        'infoAdicionais' => [
            [
                'nome' => 'Observacoes',
                'valor' => 'Compra direta sem cupom de desconto',
            ],
        ],
    ];

    try {
        $api = Gerencianet::getInstance($options);
        $pix = $api->pixCreateImmediateCharge([], $body);

        if (!isset($pix['txid'])) {
            throw new Exception('Erro ao realizar pagamento, tente novamente');
        }

        $deposito = new Deposito();
        $deposito->txid = $pix['txid'];
        $deposito->valor = $amountUsd;
        $deposito->user_id = auth()->user()->id;
        $deposito->save();

        $params = [
            'id' => $pix['loc']['id'],
        ];

        $qrcode = $api->pixGenerateQRCode($params);

        // Procurar o campo linkVisualizacao no final do JSON
        $url = null;
        $jsonString = json_encode($qrcode);
        $jsonArray = json_decode($jsonString, true);

        array_walk_recursive($jsonArray, function($value, $key) use (&$url) {
            if ($key === 'linkVisualizacao' && strpos($value, 'https://pix') === 0) {
                $url = $value;
            }
        });

        if ($url) {
            return redirect()->away($url);
        } else {
            return view('qrcode', ['qrcode' => $qrcode['imagemQrcode']]);
        }

    } catch (GerencianetException $gerencianetException) {
        return response()->json([
            'error' => $gerencianetException->getMessage()
        ], 500);
    } catch (Exception $exception) {
        return response()->json([
            'error' => $exception->getMessage()
        ], 500);
    }
}



    public function consultarPixRecebidosUltimos30Minutos()
    {
        try {
            // Configurações da API Gerencianet
            $mode = config('gerencianet.mode');
            $certificate = config("gerencianet.{$mode}.certificate_name");

            $options = [
                'client_id' => config("gerencianet.{$mode}.client_id"),
                'client_secret' => config("gerencianet.{$mode}.client_secret"),
                'certificate' => base_path("certs/{$certificate}"),
                'sandbox' => $mode === 'sandbox',
                'debug' => config('gerencianet.debug'),
                'timeout' => 30,
            ];

            // Calcula o timestamp para 30 minutos atrás e agora
            $inicio = now()->subMinutes(60)->toIso8601ZuluString();
            $fim = now()->toIso8601ZuluString();

            // Parâmetros da consulta PIX recebidos
            $params = [
                'inicio' => $inicio,
                'fim' => $fim,
            ];

            // Instancia a API Gerencianet
            $api = Gerencianet::getInstance($options);

            // Consulta PIX recebidos nos últimos 30 minutos
            $pixRecebidos = $api->pixReceivedList($params);

            // Verifica os txids encontrados na resposta da Gerencianet
            if (isset($pixRecebidos['pix'])) {
                foreach ($pixRecebidos['pix'] as $pix) {
                    $txid = $pix['txid'];

                    // Busca por depósitos com o mesmo txid e status 0 do dia atual
                    $deposito = Deposito::where('txid', $txid)
                        ->whereDate('created_at', now()->toDateString())
                        ->where('status', 0)
                        ->first();

                    if ($deposito) {
                        // Atualiza o status do depósito para 1
                        $deposito->status = 1;
                        $deposito->save();

                        // Adiciona o valor do depósito ao crédito do usuário
                        $user = User::find($deposito->user_id);
                        $user->credit += $deposito->valor;
                        $user->save();
                    }
                }
            }

            return response()->json([
                'pixRecebidos' => $pixRecebidos,
            ]);

        } catch (GerencianetException $gerencianetException) {
            return response()->json([
                'error' => $gerencianetException->getMessage()
            ], 500);
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function checkPaymentStatus()
    {
        $txid = '9c29f942f01244f182746722f9d5e59a'; // Substitua pelo txid desejado

        try {
            // Configurações da API Gerencianet
            $mode = config('gerencianet.mode');
            $certificate = config("gerencianet.{$mode}.certificate_name");

            $options = [
                'client_id' => config("gerencianet.{$mode}.client_id"),
                'client_secret' => config("gerencianet.{$mode}.client_secret"),
                'certificate' => base_path("certs/{$certificate}"),
                'sandbox' => $mode === 'sandbox',
                'debug' => config('gerencianet.debug'),
                'timeout' => 30,
            ];

            // Instancia a API Gerencianet
            $api = Gerencianet::getInstance($options);

            // Parâmetros para consultar o status do PIX
            $params = [
                'txid' => $txid,
            ];

            // Consulta o status do PIX
            $pixInfo = $api->pixDetail($params);

            return response()->json([
                'pixInfo' => $pixInfo,
            ]);

        } catch (GerencianetException $gerencianetException) {
            return response()->json([
                'error' => $gerencianetException->getMessage()
            ], 500);
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }

}
