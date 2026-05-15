<?php

use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('vendas.index');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AutenticacaoController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AutenticacaoController::class, 'autenticar'])->name('login.autenticar');
});

Route::post('/logout', [AutenticacaoController::class, 'sair'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/vendas/produtos-json', [VendaController::class, 'produtosJson'])->name('vendas.produtos_json');
    Route::get('/vendas/{venda}/pdf', [VendaController::class, 'pdf'])->name('vendas.pdf');
    Route::resource('vendas', VendaController::class)->except(['show']);
    Route::resource('clientes', ClienteController::class)->except(['show']);
});
