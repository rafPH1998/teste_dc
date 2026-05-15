<?php

namespace App\Http\Controllers;

use App\Models\FormaPagamento;
use App\Models\Produto;
use App\Services\ClienteService;
use App\Services\VendaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use InvalidArgumentException;

class VendaController extends Controller
{
    public function __construct(
        private readonly VendaService $vendaService,
        private readonly ClienteService $clienteService
    ) {}

    public function index(Request $request): View
    {
        $filtros = [
            'data_inicio' => $request->query('data_inicio'),
            'data_fim' => $request->query('data_fim'),
            'cliente_id' => $request->query('cliente_id'),
            'forma_pagamento_id' => $request->query('forma_pagamento_id'),
        ];

        $vendas = $this->vendaService->listarDoVendedorComFiltros(
            (int) $request->user()->id,
            $filtros,
            12
        );

        $clientes = $this->clienteService->listarTodosOrdenados();
        $formasPagamento = FormaPagamento::listarTodasOrdenadas();

        return view('vendas.index', compact('vendas', 'clientes', 'formasPagamento', 'filtros'));
    }

    public function create(): View
    {
        $clientes = $this->clienteService->listarTodosOrdenados();
        $formasPagamento = FormaPagamento::listarTodasOrdenadas();
        $produtos = Produto::listarParaSelect();

        return view('vendas.create', compact('clientes', 'formasPagamento', 'produtos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $dadosValidados = $request->validate([
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'forma_pagamento_id' => ['required', 'exists:formas_pagamento,id'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'exists:produtos,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
            'itens.*.preco_unitario' => ['required', 'numeric', 'min:0'],
            'parcelas' => ['required', 'array', 'min:1'],
            'parcelas.*.data_vencimento' => ['required', 'date'],
            'parcelas.*.valor' => ['required', 'numeric', 'min:0.01'],
        ]);

        $payload = [
            'cliente_id' => $dadosValidados['cliente_id'] ?? null,
            'forma_pagamento_id' => (int) $dadosValidados['forma_pagamento_id'],
            'itens' => $dadosValidados['itens'],
            'parcelas' => $dadosValidados['parcelas'],
        ];

        try {
            $this->vendaService->criar($payload, (int) $request->user()->id);
        } catch (InvalidArgumentException $erro) {
            return back()->withInput()->withErrors(['geral' => $erro->getMessage()]);
        }

        return redirect()->route('vendas.index')->with('sucesso', 'Venda registrada.');
    }

    public function edit(Request $request, int $venda): View
    {
        $registro = $this->vendaService->buscarPorIdDoVendedor($venda, (int) $request->user()->id);

        if ($registro === null) {
            abort(404);
        }

        $clientes = $this->clienteService->listarTodosOrdenados();
        $formasPagamento = FormaPagamento::listarTodasOrdenadas();
        $produtos = Produto::listarParaSelect();

        return view('vendas.edit', [
            'venda' => $registro,
            'clientes' => $clientes,
            'formasPagamento' => $formasPagamento,
            'produtos' => $produtos,
        ]);
    }

    public function update(Request $request, int $venda): RedirectResponse
    {
        $dadosValidados = $request->validate([
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'forma_pagamento_id' => ['required', 'exists:formas_pagamento,id'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'exists:produtos,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
            'itens.*.preco_unitario' => ['required', 'numeric', 'min:0'],
            'parcelas' => ['required', 'array', 'min:1'],
            'parcelas.*.data_vencimento' => ['required', 'date'],
            'parcelas.*.valor' => ['required', 'numeric', 'min:0.01'],
        ]);

        $payload = [
            'cliente_id' => $dadosValidados['cliente_id'] ?? null,
            'forma_pagamento_id' => (int) $dadosValidados['forma_pagamento_id'],
            'itens' => $dadosValidados['itens'],
            'parcelas' => $dadosValidados['parcelas'],
        ];

        try {
            $this->vendaService->atualizar($venda, $payload, (int) $request->user()->id);
        } catch (InvalidArgumentException $erro) {
            return back()->withInput()->withErrors(['geral' => $erro->getMessage()]);
        }

        return redirect()->route('vendas.index')->with('sucesso', 'Venda atualizada.');
    }

    public function destroy(Request $request, int $venda): RedirectResponse
    {
        $this->vendaService->excluir($venda, (int) $request->user()->id);

        return redirect()->route('vendas.index')->with('sucesso', 'Venda excluída.');
    }

    public function pdf(Request $request, int $venda): Response
    {
        return $this->vendaService->respostaPdfResumo($venda, (int) $request->user()->id);
    }

    public function produtosJson(): JsonResponse
    {
        return response()->json(Produto::listarParaSelect());
    }
}
