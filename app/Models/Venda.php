<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venda extends Model
{
    protected $fillable = [
        'user_id',
        'cliente_id',
        'forma_pagamento_id',
        'valor_total',
    ];

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2',
        ];
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(VendaItem::class);
    }

    public function parcelas(): HasMany
    {
        return $this->hasMany(ParcelaVenda::class)->orderBy('numero_parcela');
    }

    public function scopeComFiltrosListagem($consulta, array $filtros)
    {
        if (! empty($filtros['data_inicio'])) {
            $consulta->whereDate('created_at', '>=', $filtros['data_inicio']);
        }

        if (! empty($filtros['data_fim'])) {
            $consulta->whereDate('created_at', '<=', $filtros['data_fim']);
        }

        if (! empty($filtros['cliente_id'])) {
            $consulta->where('cliente_id', $filtros['cliente_id']);
        }

        if (! empty($filtros['forma_pagamento_id'])) {
            $consulta->where('forma_pagamento_id', $filtros['forma_pagamento_id']);
        }

        return $consulta;
    }

    public static function queryComRelacionamentosParaDetalhe()
    {
        return self::query()
            ->with([
                'vendedor:id,name,email',
                'cliente:id,nome,email,cpf',
                'formaPagamento:id,nome',
                'itens.produto:id,nome',
                'parcelas',
            ]);
    }
}
