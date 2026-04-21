<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TipoProduto extends Model
{
    protected $fillable = [
        'status',
        'nome',
    ];

    /**
     * Relacionamento
     * Um tipo de produto pertence a vários produtos
     */
    /* public function produto() {
        return $this->hasMany(Produto::class);
    } */

    protected function statusFormatada(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['status'] ? 'Ativo' : 'Inativo',
        );
    }
}
