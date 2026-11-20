<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function produto() {
        return $this->hasMany(Produto::class);
    }

    /**
     * Fitros do tipo produtos 
    */
    public function scopeFiltroCodigo($query, $codigo) {
        if ($codigo) {
            $query->where('id', $codigo);
        }

        return $query;
    }

    public function scopeFiltroStatus($query, $status) {
        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        return $query;
    }

    public function scopeFiltroNome($query, $nome) {
        if ($nome) {
            $query->where('nome', 'like', "%{$nome}%");
        }

        return $query;
    }
}
