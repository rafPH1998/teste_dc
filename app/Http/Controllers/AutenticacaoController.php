<?php

namespace App\Http\Controllers;

use App\Services\AutenticacaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutenticacaoController extends Controller
{
    public function __construct(
        private readonly AutenticacaoService $autenticacaoService
    ) {}

    public function mostrarLogin(): View
    {
        return view('auth.login');
    }

    public function autenticar(Request $request): RedirectResponse
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $lembrar = $request->boolean('remember');

        if (! $this->autenticacaoService->tentarLogin($credenciais, $lembrar)) {
            return back()
                ->withErrors(['email' => 'Credenciais inválidas.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('vendas.index'));
    }

    public function sair(Request $request): RedirectResponse
    {
        $this->autenticacaoService->encerrarSessao();

        return redirect()->route('login');
    }
}
