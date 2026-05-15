@extends('layouts.app')

@section('titulo', 'Vendas')

@section('conteudo')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h3 page-heading mb-1">Vendas</h1>
        <p class="text-muted mb-0 small">Suas vendas registradas com filtros e exportação em PDF.</p>
    </div>
    <a href="{{ route('vendas.create') }}" class="btn btn-dc-primary text-white rounded-pill px-4">Nova venda</a>
</div>

<div class="card card-dc mb-4">
    <div class="card-body">
        <form method="get" action="{{ route('vendas.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1" for="data_inicio">Data inicial</label>
                <input type="date" name="data_inicio" id="data_inicio" class="form-control"
                       value="{{ $filtros['data_inicio'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1" for="data_fim">Data final</label>
                <input type="date" name="data_fim" id="data_fim" class="form-control"
                       value="{{ $filtros['data_fim'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1" for="cliente_id">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" @selected(($filtros['cliente_id'] ?? '') == $cliente->id)>{{ $cliente->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1" for="forma_pagamento_id">Forma de pagamento</label>
                <select name="forma_pagamento_id" id="forma_pagamento_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach($formasPagamento as $forma)
                        <option value="{{ $forma->id }}" @selected(($filtros['forma_pagamento_id'] ?? '') == $forma->id)>{{ $forma->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary rounded-pill px-4">Filtrar</button>
                <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-dc">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Forma</th>
                        <th class="text-end">Total</th>
                        <th class="text-end" style="min-width: 220px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendas as $venda)
                        <tr>
                            <td class="text-muted">{{ $venda->id }}</td>
                            <td>{{ $venda->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $venda->cliente?->nome ?? '—' }}</td>
                            <td><span class="badge badge-soft">{{ $venda->formaPagamento->nome }}</span></td>
                            <td class="text-end fw-semibold">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                            <td class="text-end">
                                <a href="{{ route('vendas.pdf', $venda) }}" class="btn btn-sm btn-outline-secondary rounded-pill" target="_blank">PDF</a>
                                <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-sm btn-outline-primary rounded-pill">Editar</a>
                                <form action="{{ route('vendas.destroy', $venda) }}" method="post" class="d-inline"
                                      onsubmit="return confirm('Excluir esta venda?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Nenhuma venda encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vendas->hasPages())
        <div class="card-footer bg-white border-0">{{ $vendas->links() }}</div>
    @endif
</div>
@endsection
