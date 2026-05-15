<?php

namespace Database\Seeders;

use App\Models\FormaPagamento;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'vendedor@teste.local'],
            [
                'name' => 'Vendedor Demo',
                'password' => bcrypt('senha123'),
            ]
        );

        $formas = ['Dinheiro', 'PIX', 'Cartão à vista', 'Cartão parcelado', 'Boleto'];

        foreach ($formas as $nome) {
            FormaPagamento::query()->firstOrCreate(['nome' => $nome]);
        }

        $produtos = [
            ['nome' => 'Notebook 15"', 'preco' => 3499.90],
            ['nome' => 'Mouse sem fio', 'preco' => 89.90],
            ['nome' => 'Teclado mecânico', 'preco' => 459.00],
            ['nome' => 'Monitor 24"', 'preco' => 899.00],
            ['nome' => 'Webcam HD', 'preco' => 249.90],
        ];

        foreach ($produtos as $produto) {
            Produto::query()->firstOrCreate(
                ['nome' => $produto['nome']],
                ['preco' => $produto['preco'], 'ativo' => true]
            );
        }
    }
}
