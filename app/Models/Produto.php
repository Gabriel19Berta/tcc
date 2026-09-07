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

    /**
     * Filtro de produtos
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

    public function scopeFiltroMarca($query, $marcaId) 
    {
        if ($marcaId) {
            $query->where('marca_id', $marcaId);
        }

        return $query;
    }

    public function scopeFiltroTipoProduto($query, $tipoProdutoId) 
    {
        if ($tipoProdutoId) {
            $query->where('tipo_produto_id', $tipoProdutoId);
        }

        return $query;
    }
}