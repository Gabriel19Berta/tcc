<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $fillable = [
        'status',
        'nome',
    ];

    /**
     * Relacionamento
     * Uma marca pertence a vários produtos
     */
    public function produto() {
        return $this->hasMany(Produto::class);
    }

    /**
     * Filtros de marca
     */
    public function scopeFiltroCodigo($query, $codigo)
    {
        if ($codigo) {
            $query->where('id', $codigo);
        }

        return $query;
    }

    public function scopeFiltroStatus($query, $status)
    {
        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        return $query;
    }

    public function scopeFiltroNome($query, $nome)
    {
        if ($nome) {
            $query->where('nome', 'like', "%{$nome}%");
        }

        return $query;
    }
}
