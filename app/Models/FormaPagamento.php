<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormaPagamento extends Model
{
    protected $table = 'formas_pagamento';

    protected $fillable = [
        'nome',
    ];

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }

    public static function listarTodasOrdenadas()
    {
        return self::query()->orderBy('nome')->get();
    }
}
