@extends('layouts.app')

@section('titulo', 'Editar cliente')

@section('conteudo')
<div class="mb-4">
    <a href="{{ route('clientes.index') }}" class="text-decoration-none small text-muted">← Voltar</a>
    <h1 class="h3 page-heading mt-2">Editar cliente</h1>
</div>

<div class="card card-dc">
    <div class="card-body p-4">
        <form method="post" action="{{ route('clientes.update', $cliente) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label" for="nome">Nome</label>
                <input type="text" name="nome" id="nome" class="form-control form-control-lg @error('nome') is-invalid @enderror"
                       value="{{ old('nome', $cliente->nome) }}" required maxlength="255">
                @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">E-mail <span class="text-muted">(opcional)</span></label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $cliente->email) }}" maxlength="255">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label" for="cpf">CPF <span class="text-muted">(opcional)</span></label>
                <input type="text" name="cpf" id="cpf" class="form-control @error('cpf') is-invalid @enderror"
                       value="{{ old('cpf', $cliente->cpf) }}" maxlength="14">
                @error('cpf')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-dc-primary text-white rounded-pill px-4">Atualizar</button>
        </form>
    </div>
</div>
@endsection
