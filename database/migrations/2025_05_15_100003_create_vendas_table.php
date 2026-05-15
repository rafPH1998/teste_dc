<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('forma_pagamento_id')->constrained('formas_pagamento')->restrictOnDelete();
            $table->decimal('valor_total', 14, 2);
            $table->timestamps();

            $table->index(['created_at']);
            $table->index(['cliente_id']);
            $table->index(['forma_pagamento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
