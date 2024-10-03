<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    /**
     * Mostra a página para editar o crédito do usuário.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        return view('credits.edit', compact('user'));
    }

    /**
     * Atualiza o crédito do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $userId)
    {
        $request->validate([
            'credit' => 'required|integer|min:0',
        ]);

        $user = User::findOrFail($userId);
        $user->credit = $request->credit;
        $user->save();

        return redirect()->route('credits.show')
                         ->with('success', 'Crédito atualizado com sucesso.');
    }

    /**
     * Retorna o total de créditos do usuário logado.
     *
     * @return int
     */
    public function getTotalCredits()
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        return $user->credit;
    }
}
