<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $fillable = [
        'nome',
        'preco',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }

    public function vendaItens(): HasMany
    {
        return $this->hasMany(VendaItem::class);
    }

    public function scopeSomenteAtivos($consulta)
    {
        return $consulta->where('ativo', true);
    }

    public static function listarParaSelect()
    {
        return self::query()
            ->somenteAtivos()
            ->orderBy('nome')
            ->get(['id', 'nome', 'preco']);
    }
}
