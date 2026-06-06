<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Produto extends Model
{
    protected $fillable = [
        'marca_id',
        'tipo_produto_id',
        'status',
        'nome',
        'preco_venda',
        'preco_custo',
        'controla_estoque',
        'quantidade',
        'peso',
        'observacoes'
    ];

    /**
     *  Relacionamento
     *  Um produto pode ter uma marca
     */
    public function marca() {
        return $this->belongsTo(Marca::class);
    }

    /**
     *  Relacionamento
     *  Um produto pode ter um tipo produto
     */
    public function tipoProduto() {
        return $this->belongsTo(TipoProduto::class);
    }

    protected function statusFormatada(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['status'] ? 'Ativo' : 'Inativo',
        );
    }
}