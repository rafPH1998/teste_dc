<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelas_venda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
            $table->unsignedInteger('numero_parcela');
            $table->date('data_vencimento');
            $table->decimal('valor', 14, 2);
            $table->timestamps();

            $table->unique(['venda_id', 'numero_parcela']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelas_venda');
    }
};
