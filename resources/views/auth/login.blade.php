@extends('layouts.app')

@section('titulo', 'Entrar')

@section('conteudo')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card card-dc p-4">
            <div class="text-center mb-4">
                <img src="{{ asset('logo-dc/logo-dc.png') }}" alt="Logo" class="logo-login">
            </div>
            <h1 class="h5 page-heading mb-3 text-center">Entrar</h1>
            <form method="post" action="{{ route('login.autenticar') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">E-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Senha</label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                    <label class="form-check-label" for="remember">Lembrar-me</label>
                </div>
                <button type="submit" class="btn btn-dc-primary w-100">Entrar</button>
            </form>
            <p class="text-muted small mt-3 mb-0 text-center">
                Demo: <code>vendedor@teste.local</code> / <code>senha123</code>
            </p>
        </div>
    </div>
</div>
@endsection
