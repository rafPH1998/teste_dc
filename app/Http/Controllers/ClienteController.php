<?php

namespace App\Http\Controllers;

use App\Services\ClienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function __construct(
        private readonly ClienteService $clienteService
    ) {}

    public function index(): View
    {
        $clientes = $this->clienteService->listarPaginado(12);

        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:clientes,cpf'],
        ]);

        $this->clienteService->criar($dados);

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente cadastrado.');
    }

    public function edit(int $cliente): View
    {
        $registro = $this->clienteService->buscarPorId($cliente);

        if ($registro === null) {
            abort(404);
        }

        return view('clientes.edit', ['cliente' => $registro]);
    }

    public function update(Request $request, int $cliente): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:clientes,cpf,'.$cliente],
        ]);

        $this->clienteService->atualizar($cliente, $dados);

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente atualizado.');
    }

    public function destroy(int $cliente): RedirectResponse
    {
        $this->clienteService->excluir($cliente);

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente removido.');
    }
}
