<?php



namespace App\Http\Controllers;

use App\Domain\Dhru\Services\DhruCatalogListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ImeiService;
use App\Models\Page;
use Illuminate\Support\Facades\Log;
class IMEIController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
 /**
 * Exibe os serviços de IMEI na view imei.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
 */
public function showIMEIServices(Request $request)
{
    $filteredServices = app(DhruCatalogListingService::class)->getServicesForType('IMEI');

    $page = Page::where('slug', 'imei-services')->where('is_active', true)->firstOrFail();

    $blocks = $page->blocks()
        ->where('is_visible', true)
        ->with('plugin')
        ->orderBy('sort_order')
        ->get();

    $pluginContext = [
        'filteredServices' => $filteredServices,
    ];

    return view('page-builder', compact('page', 'blocks', 'pluginContext'));
}





/**
 * Mostra o histórico de IMEI para o usuário logado.
 *
 * @return \Illuminate\View\View
 */
public function showIMEIHistory()
{
    try {
        $userId = Auth::id();
        Log::info('ID do usuário logado:', ['userId' => $userId]);

        $imeiOrders = ImeiService::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(10);

        Log::info('Ordens de IMEI recuperadas:', ['imeiOrders' => $imeiOrders]);

        $page = Page::where('slug', 'imei-history')->where('is_active', true)->firstOrFail();

        $blocks = $page->blocks()
            ->where('is_visible', true)
            ->with('plugin')
            ->orderBy('sort_order')
            ->get();

        $pluginContext = [
            'imeiOrders' => $imeiOrders,
        ];

        return view('page-builder', compact('page', 'blocks', 'pluginContext'));

    } catch (\Exception $e) {
        Log::error('Erro ao recuperar histórico de IMEI:', ['exception' => $e]);
        return back()->withError('Erro ao recuperar histórico de IMEI. Por favor, tente novamente mais tarde.');
    }
}
}
