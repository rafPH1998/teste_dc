<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ClienteService
{
    public function listarPaginado(int $porPagina = 15): LengthAwarePaginator
    {
        return Cliente::query()
            ->ordenadosPorNome()
            ->paginate($porPagina);
    }

    public function listarTodosOrdenados(): Collection
    {
        return Cliente::query()->ordenadosPorNome()->get();
    }

    public function buscarPorId(int $id): ?Cliente
    {
        return Cliente::query()->find($id);
    }

    public function criar(array $dados): Cliente
    {
        return Cliente::query()->create([
            'nome' => $dados['nome'],
            'email' => $this->normalizarOpcional($dados['email'] ?? null),
            'cpf' => $this->normalizarOpcional($dados['cpf'] ?? null),
        ]);
    }

    public function atualizar(int $id, array $dados): Cliente
    {
        $cliente = Cliente::query()->findOrFail($id);
        $cliente->update([
            'nome' => $dados['nome'],
            'email' => $this->normalizarOpcional($dados['email'] ?? null),
            'cpf' => $this->normalizarOpcional($dados['cpf'] ?? null),
        ]);

        return $cliente->fresh();
    }

    public function excluir(int $id): void
    {
        Cliente::query()->whereKey($id)->delete();
    }

    private function normalizarOpcional(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $texto = trim($valor);

        return $texto === '' ? null : $texto;
    }
}
