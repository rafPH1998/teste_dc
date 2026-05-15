<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'cpf',
    ];

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }

    public function scopeOrdenadosPorNome($consulta)
    {
        return $consulta->orderBy('nome');
    }
}
