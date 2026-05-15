<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin: 0 0 12px; color: #c2410c; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #ffedd5; }
        .text-right { text-align: right; }
        .muted { color: #666; font-size: 11px; }
        .box { margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>Resumo da venda #{{ $venda->id }}</h1>
    <p class="muted">Emitido em {{ now()->format('d/m/Y H:i') }}</p>

    <div class="box">
        <strong>Vendedor:</strong> {{ $venda->vendedor->name }} ({{ $venda->vendedor->email }})<br>
        <strong>Cliente:</strong> {{ $venda->cliente?->nome ?? '—' }}<br>
        @if($venda->cliente?->email)
            <strong>E-mail cliente:</strong> {{ $venda->cliente->email }}<br>
        @endif
        @if($venda->cliente?->cpf)
            <strong>CPF cliente:</strong> {{ $venda->cliente->cpf }}<br>
        @endif
        <strong>Forma de pagamento:</strong> {{ $venda->formaPagamento->nome }}<br>
        <strong>Data da venda:</strong> {{ $venda->created_at->format('d/m/Y H:i') }}
    </div>

    <h2 style="font-size: 14px; margin-bottom: 4px;">Itens</h2>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th class="text-right">Qtd</th>
                <th class="text-right">Preço unit.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venda->itens as $item)
                <tr>
                    <td>{{ $item->produto->nome }}</td>
                    <td class="text-right">{{ $item->quantidade }}</td>
                    <td class="text-right">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total</th>
                <th class="text-right">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <h2 style="font-size: 14px; margin: 20px 0 4px;">Parcelas</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Vencimento</th>
                <th class="text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venda->parcelas as $parcela)
                <tr>
                    <td>{{ $parcela->numero_parcela }}</td>
                    <td>{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                    <td class="text-right">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
