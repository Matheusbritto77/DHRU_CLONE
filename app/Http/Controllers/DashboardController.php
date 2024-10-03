<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ImeiService;
use App\Models\server_services;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = Auth::user();

        // Total de crédito atual do usuário logado
        $totalCredit = $user->credit;

        // Total de ordens de sucesso (status = 4) e rejeitadas (status = 3) em imei_services
        $imeiSuccessCount = ImeiService::where('user_id', $user->id)->where('status', 4)->count();
        $imeiRejectedCount = ImeiService::where('user_id', $user->id)->where('status', 3)->count();

        // Total de ordens de sucesso (status = 4) e rejeitadas (status = 3) em server_services
        $serverSuccessCount = server_services::where('user_id', $user->id)->where('status', 4)->count();
        $serverRejectedCount = server_services::where('user_id', $user->id)->where('status', 3)->count();

        // Total de ordens enviadas em imei_services e server_services
        $totalIMEIOrders = ImeiService::where('user_id', $user->id)->count();
        $totalServerOrders = server_services::where('user_id', $user->id)->count();

        // Passa os dados para a view do dashboard
        return view('dashboard', [
            'totalCredit' => $totalCredit,
            'imeiSuccessCount' => $imeiSuccessCount,
            'imeiRejectedCount' => $imeiRejectedCount,
            'serverSuccessCount' => $serverSuccessCount,
            'serverRejectedCount' => $serverRejectedCount,
            'totalIMEIOrders' => $totalIMEIOrders,
            'totalServerOrders' => $totalServerOrders,
        ]);
    }
}
