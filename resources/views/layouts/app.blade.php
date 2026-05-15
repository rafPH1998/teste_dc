<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Vendas') — Teste DC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        :root {
            --dc-laranja: #f26522;
            --dc-laranja-escuro: #d94d0c;
            --dc-topo: #0f0f0f;
            --dc-bg: #f5f5f5;
            --bs-primary: #f26522;
            --bs-primary-rgb: 242, 101, 34;
        }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: var(--dc-bg);
            min-height: 100vh;
        }
        .topo-dc {
            background: var(--dc-topo);
            border-bottom: 1px solid #000;
        }
        .topo-dc .nav-link {
            color: #e5e5e5 !important;
            font-weight: 500;
            border-radius: 0.25rem;
            padding: 0.4rem 0.75rem !important;
        }
        .topo-dc .nav-link:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.06);
        }
        .topo-dc .nav-link.active {
            color: #fff !important;
            background: rgba(242, 101, 34, 0.22);
        }
        .topo-dc .navbar-brand {
            color: #fff !important;
        }
        .logo-dc {
            height: 38px;
            width: auto;
            vertical-align: middle;
        }
        .card-dc {
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            background: #fff;
        }
        .btn-dc-primary {
            background: var(--dc-laranja);
            border-color: var(--dc-laranja);
            color: #fff !important;
        }
        .btn-dc-primary:hover {
            background: var(--dc-laranja-escuro);
            border-color: var(--dc-laranja-escuro);
            color: #fff !important;
        }
        .page-heading { font-weight: 700; color: #333; }
        .badge-soft {
            background: #ffe8dc;
            color: #b3470a;
            font-weight: 600;
        }
        .text-dc-accent { color: var(--dc-laranja-escuro) !important; }
        .logo-login {
            max-height: 64px;
            width: auto;
        }
    </style>
    @stack('estilos')
</head>
<body>
@if(auth()->check())
    <nav class="navbar navbar-expand-lg navbar-dark topo-dc mb-3">
        <div class="container-fluid px-3">
            <a class="navbar-brand d-flex align-items-center py-1" href="{{ route('vendas.index') }}">
                <img src="{{ asset('logo-dc/logo-dc.png') }}" alt="Logo" class="logo-dc">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navPrincipal">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navPrincipal">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('vendas.*')) active @endif" href="{{ route('vendas.index') }}">Vendas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('clientes.*')) active @endif" href="{{ route('clientes.index') }}">Clientes</a>
                    </li>
                </ul>
                <span class="navbar-text text-white-50 me-3 small">
                    {{ auth()->user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light">Sair</button>
                </form>
            </div>
        </div>
    </nav>
@endif

<main class="container pb-5">
    @if(session('sucesso'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('sucesso') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('conteudo')
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
