<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelaVenda extends Model
{
    protected $table = 'parcelas_venda';

    protected $fillable = [
        'venda_id',
        'numero_parcela',
        'data_vencimento',
        'valor',
    ];

    protected function casts(): array
    {
        return [
            'data_vencimento' => 'date',
            'valor' => 'decimal:2',
        ];
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }
}
