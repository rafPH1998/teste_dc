@extends('layouts.app')

@section('titulo', 'Editar venda')

@section('conteudo')
@php
    $itensIniciais = old('itens');
    if (! is_array($itensIniciais) || count($itensIniciais) === 0) {
        $itensIniciais = $venda->itens->map(function ($item) {
            return [
                'produto_id' => $item->produto_id,
                'quantidade' => $item->quantidade,
                'preco_unitario' => $item->preco_unitario,
            ];
        })->all();
    }
    $parcelasIniciais = old('parcelas');
    if (! is_array($parcelasIniciais) || count($parcelasIniciais) === 0) {
        $parcelasIniciais = $venda->parcelas->map(function ($parcela) {
            return [
                'data_vencimento' => $parcela->data_vencimento->format('Y-m-d'),
                'valor' => $parcela->valor,
            ];
        })->all();
    }
@endphp
<div class="mb-4">
    <a href="{{ route('vendas.index') }}" class="text-decoration-none small text-muted">← Voltar</a>
    <h1 class="h3 page-heading mt-2">Editar venda #{{ $venda->id }}</h1>
</div>

<form method="post" action="{{ route('vendas.update', $venda) }}" id="form_venda">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-dc h-100">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Cabeçalho</h2>
                    <div class="mb-3">
                        <label class="form-label" for="cliente_id">Cliente <span class="text-muted">(opcional)</span></label>
                        <select name="cliente_id" id="cliente_id" class="form-select">
                            <option value="">— Sem cliente —</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected(old('cliente_id', $venda->cliente_id) == $cliente->id)>{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="forma_pagamento_id">Forma de pagamento</label>
                        <select name="forma_pagamento_id" id="forma_pagamento_id" class="form-select @error('forma_pagamento_id') is-invalid @enderror" required>
                            <option value="">Selecione...</option>
                            @foreach($formasPagamento as $forma)
                                <option value="{{ $forma->id }}" @selected(old('forma_pagamento_id', $venda->forma_pagamento_id) == $forma->id)>{{ $forma->nome }}</option>
                            @endforeach
                        </select>
                        @error('forma_pagamento_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-dc mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 text-uppercase text-muted mb-0">Itens</h2>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="btn_add_item">+ Item</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle" id="tabela_itens">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th style="width:110px;">Qtd</th>
                                    <th style="width:130px;">Preço unit.</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="corpo_itens"></tbody>
                        </table>
                    </div>
                    <p class="mb-0 fw-semibold">Total dos itens: <span id="total_itens" class="text-dc-accent">R$ 0,00</span></p>
                </div>
            </div>

            <div class="card card-dc">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="h6 text-uppercase text-muted mb-0">Parcelas</h2>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="btn_add_parcela">+ Parcela</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="btn_parcelas_iguais">Dividir em N iguais</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Vencimento</th>
                                    <th style="width:160px;">Valor</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="corpo_parcelas"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-dc-primary text-white rounded-pill px-5">Atualizar venda</button>
        <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
window.produtosLista = @json($produtos);
window.itensIniciaisVenda = @json($itensIniciais);
window.parcelasIniciaisVenda = @json($parcelasIniciais);
</script>
<script>
(function ($) {
    function formatarMoeda(valor) {
        const n = Number(valor) || 0;
        return n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function montarOptionsProduto(selecionado) {
        let html = '<option value="">Selecione...</option>';
        window.produtosLista.forEach(function (p) {
            const sel = String(p.id) === String(selecionado) ? ' selected' : '';
            html += '<option value="' + p.id + '" data-preco="' + p.preco + '"' + sel + '>' + p.nome + '</option>';
        });
        return html;
    }

    function reindexarItens() {
        $('#corpo_itens tr').each(function (idx) {
            $(this).find('.campo-produto').attr('name', 'itens[' + idx + '][produto_id]');
            $(this).find('.campo-qtd').attr('name', 'itens[' + idx + '][quantidade]');
            $(this).find('.campo-preco').attr('name', 'itens[' + idx + '][preco_unitario]');
        });
    }

    function reindexarParcelas() {
        $('#corpo_parcelas tr').each(function (idx) {
            $(this).find('.campo-vencimento').attr('name', 'parcelas[' + idx + '][data_vencimento]');
            $(this).find('.campo-valor-parcela').attr('name', 'parcelas[' + idx + '][valor]');
        });
    }

    function calcularTotalItens() {
        let total = 0;
        $('#corpo_itens tr').each(function () {
            const q = parseInt($(this).find('.campo-qtd').val(), 10) || 0;
            const p = parseFloat(String($(this).find('.campo-preco').val()).replace(',', '.')) || 0;
            total += q * p;
        });
        $('#total_itens').text(formatarMoeda(total));
        return total;
    }

    function adicionarLinhaItem(dados) {
        dados = dados || {};
        const tr = $('<tr>');
        tr.append(
            '<td><select class="form-select campo-produto">' + montarOptionsProduto(dados.produto_id) + '</select></td>' +
            '<td><input type="number" min="1" class="form-control campo-qtd" value="' + (dados.quantidade || 1) + '"></td>' +
            '<td><input type="number" step="0.01" min="0" class="form-control campo-preco" value="' + (dados.preco_unitario || '') + '"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger btn_remover_item">×</button></td>'
        );
        $('#corpo_itens').append(tr);
        reindexarItens();
        calcularTotalItens();
    }

    function adicionarLinhaParcela(dados) {
        dados = dados || {};
        const tr = $('<tr>');
        tr.append(
            '<td><input type="date" class="form-control campo-vencimento" value="' + (dados.data_vencimento || '') + '"></td>' +
            '<td><input type="number" step="0.01" min="0.01" class="form-control campo-valor-parcela" value="' + (dados.valor || '') + '"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger btn_remover_parcela">×</button></td>'
        );
        $('#corpo_parcelas').append(tr);
        reindexarParcelas();
    }

    $(document).on('change', '.campo-produto', function () {
        const opt = $(this).find('option:selected');
        const preco = opt.data('preco');
        const tr = $(this).closest('tr');
        if (preco !== undefined && preco !== '') {
            tr.find('.campo-preco').val(preco);
        }
        calcularTotalItens();
    });

    $(document).on('input', '.campo-qtd, .campo-preco', function () {
        calcularTotalItens();
    });

    $(document).on('click', '.btn_remover_item', function () {
        $(this).closest('tr').remove();
        reindexarItens();
        calcularTotalItens();
    });

    $(document).on('click', '.btn_remover_parcela', function () {
        $(this).closest('tr').remove();
        reindexarParcelas();
    });

    $('#btn_add_item').on('click', function () {
        adicionarLinhaItem({});
    });

    $('#btn_add_parcela').on('click', function () {
        adicionarLinhaParcela({});
    });

    $('#btn_parcelas_iguais').on('click', function () {
        const total = calcularTotalItens();
        if (total <= 0) {
            alert('Adicione itens com quantidade e preço primeiro.');
            return;
        }
        const n = parseInt(prompt('Em quantas parcelas dividir?', '2'), 10);
        if (!n || n < 1) return;
        const base = Math.floor((total * 100) / n) / 100;
        let resto = Math.round((total - base * n) * 100) / 100;
        $('#corpo_parcelas').empty();
        const hoje = new Date();
        for (let i = 0; i < n; i++) {
            const v = i === 0 ? base + resto : base;
            const dt = new Date(hoje.getFullYear(), hoje.getMonth() + i, hoje.getDate());
            const iso = dt.toISOString().slice(0, 10);
            adicionarLinhaParcela({ data_vencimento: iso, valor: v.toFixed(2) });
        }
    });

    window.itensIniciaisVenda.forEach(function (item) {
        adicionarLinhaItem(item);
    });
    if (window.itensIniciaisVenda.length === 0) {
        adicionarLinhaItem({});
    }
    window.parcelasIniciaisVenda.forEach(function (p) {
        adicionarLinhaParcela(p);
    });
    if (window.parcelasIniciaisVenda.length === 0) {
        adicionarLinhaParcela({});
    }

    calcularTotalItens();
})(jQuery);
</script>
@endpush
