<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AutenticacaoService
{
    public function tentarLogin(array $credenciais, bool $lembrar = false): bool
    {
        return Auth::attempt($credenciais, $lembrar);
    }

    public function encerrarSessao(): void
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
