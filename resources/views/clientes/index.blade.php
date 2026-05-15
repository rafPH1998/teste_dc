@extends('layouts.app')

@section('titulo', 'Clientes')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 page-heading mb-0">Clientes</h1>
        <p class="text-muted mb-0 small">Cadastro simples com nome, e-mail e CPF.</p>
    </div>
    <a href="{{ route('clientes.create') }}" class="btn btn-dc-primary text-white rounded-pill px-4">Novo cliente</a>
</div>

<div class="card card-dc">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>CPF</th>
                        <th class="text-end" style="width: 160px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        <tr>
                            <td class="fw-semibold">{{ $cliente->nome }}</td>
                            <td>{{ $cliente->email ?? '—' }}</td>
                            <td><span class="badge badge-soft">{{ $cliente->cpf ?? '—' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary rounded-pill">Editar</a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="post" class="d-inline"
                                      onsubmit="return confirm('Remover este cliente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">Nenhum cliente cadastrado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($clientes->hasPages())
        <div class="card-footer bg-white border-0">{{ $clientes->links() }}</div>
    @endif
</div>
@endsection
