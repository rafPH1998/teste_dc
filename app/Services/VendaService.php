<?php

namespace App\Services;

use App\Models\ParcelaVenda;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\VendaItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class VendaService
{
    public function listarDoVendedorComFiltros(int $vendedorId, array $filtros, int $porPagina = 15): LengthAwarePaginator
    {
        return Venda::query()
            ->where('user_id', $vendedorId)
            ->comFiltrosListagem($filtros)
            ->with([
                'cliente:id,nome',
                'formaPagamento:id,nome',
            ])
            ->orderByDesc('created_at')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function buscarPorIdDoVendedor(int $vendaId, int $vendedorId): ?Venda
    {
        return Venda::queryComRelacionamentosParaDetalhe()
            ->where('user_id', $vendedorId)
            ->whereKey($vendaId)
            ->first();
    }

    public function criar(array $payload, int $vendedorId): Venda
    {
        return DB::transaction(function () use ($payload, $vendedorId) {
            $valorTotal = $this->calcularTotalItens($payload['itens']);
            $this->validarSomaParcelas($payload['parcelas'], $valorTotal);

            $venda = Venda::query()->create([
                'user_id' => $vendedorId,
                'cliente_id' => $payload['cliente_id'] ?? null,
                'forma_pagamento_id' => $payload['forma_pagamento_id'],
                'valor_total' => $valorTotal,
            ]);

            $this->persistirItens($venda->id, $payload['itens']);
            $this->persistirParcelas($venda->id, $payload['parcelas']);

            return $venda->load(['itens.produto', 'parcelas', 'cliente', 'formaPagamento', 'vendedor']);
        });
    }

    public function atualizar(int $vendaId, array $payload, int $vendedorId): Venda
    {
        return DB::transaction(function () use ($vendaId, $payload, $vendedorId) {
            $venda = Venda::query()
                ->where('user_id', $vendedorId)
                ->whereKey($vendaId)
                ->lockForUpdate()
                ->firstOrFail();

            $valorTotal = $this->calcularTotalItens($payload['itens']);
            $this->validarSomaParcelas($payload['parcelas'], $valorTotal);

            $venda->update([
                'cliente_id' => $payload['cliente_id'] ?? null,
                'forma_pagamento_id' => $payload['forma_pagamento_id'],
                'valor_total' => $valorTotal,
            ]);

            VendaItem::query()->where('venda_id', $venda->id)->delete();
            ParcelaVenda::query()->where('venda_id', $venda->id)->delete();

            $this->persistirItens($venda->id, $payload['itens']);
            $this->persistirParcelas($venda->id, $payload['parcelas']);

            return $venda->fresh()->load(['itens.produto', 'parcelas', 'cliente', 'formaPagamento', 'vendedor']);
        });
    }

    public function excluir(int $vendaId, int $vendedorId): void
    {
        $afetadas = Venda::query()
            ->where('user_id', $vendedorId)
            ->whereKey($vendaId)
            ->delete();

        if ($afetadas === 0) {
            abort(404);
        }
    }

    public function respostaPdfResumo(int $vendaId, int $vendedorId): Response
    {
        $venda = $this->buscarPorIdDoVendedor($vendaId, $vendedorId);

        if ($venda === null) {
            abort(404);
        }

        $pdf = Pdf::loadView('pdf.resumo_venda', ['venda' => $venda])
            ->setPaper('a4');

        $nomeArquivo = 'venda_'.$venda->id.'_resumo.pdf';

        return $pdf->stream($nomeArquivo);
    }

    private function calcularTotalItens(array $itens): string
    {
        if (count($itens) === 0) {
            throw new InvalidArgumentException('Informe ao menos um item na venda.');
        }

        $total = '0.00';

        foreach ($itens as $item) {
            $produtoId = (int) $item['produto_id'];
            $quantidade = (int) $item['quantidade'];
            $precoUnitario = (string) $item['preco_unitario'];

            if ($quantidade < 1) {
                throw new InvalidArgumentException('Quantidade inválida para o produto '.$produtoId.'.');
            }

            if (! Produto::query()->whereKey($produtoId)->where('ativo', true)->exists()) {
                throw new InvalidArgumentException('Produto inválido ou inativo: '.$produtoId.'.');
            }

            $subtotal = bcmul($precoUnitario, (string) $quantidade, 2);
            $total = bcadd($total, $subtotal, 2);
        }

        return $total;
    }

    private function validarSomaParcelas(array $parcelas, string $valorTotalVenda): void
    {
        if (count($parcelas) === 0) {
            throw new InvalidArgumentException('Informe ao menos uma parcela.');
        }

        $somaParcelas = '0.00';

        foreach ($parcelas as $parcela) {
            $somaParcelas = bcadd($somaParcelas, (string) $parcela['valor'], 2);
        }

        if (bccomp($somaParcelas, $valorTotalVenda, 2) !== 0) {
            throw new InvalidArgumentException('A soma das parcelas deve ser igual ao total dos itens (R$ '.$valorTotalVenda.').');
        }
    }

    private function persistirItens(int $vendaId, array $itens): void
    {
        foreach ($itens as $item) {
            $quantidade = (int) $item['quantidade'];
            $precoUnitario = (string) $item['preco_unitario'];
            $subtotal = bcmul($precoUnitario, (string) $quantidade, 2);

            VendaItem::query()->create([
                'venda_id' => $vendaId,
                'produto_id' => (int) $item['produto_id'],
                'quantidade' => $quantidade,
                'preco_unitario' => $precoUnitario,
                'subtotal' => $subtotal,
            ]);
        }
    }

    private function persistirParcelas(int $vendaId, array $parcelas): void
    {
        $numero = 1;

        foreach ($parcelas as $parcela) {
            ParcelaVenda::query()->create([
                'venda_id' => $vendaId,
                'numero_parcela' => $numero,
                'data_vencimento' => $parcela['data_vencimento'],
                'valor' => $parcela['valor'],
            ]);
            $numero++;
        }
    }
}
