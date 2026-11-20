<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Booleanos como true/false (e não 0/1) e valores monetários e de estoque
     * com 2 casas decimais, igual às colunas e aos models de venda.
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'controla_estoque' => 'boolean',
            'preco_venda' => 'decimal:2',
            'preco_custo' => 'decimal:2',
            'quantidade' => 'decimal:2',
        ];
    }

    /**
     *  Relacionamento
     *  Um produto pode ter uma marca
     */
    public function marca() :BelongsTo {
        return $this->belongsTo(Marca::class);
    }

    /**
     *  Relacionamento
     *  Um produto pode ter um tipo produto
     */
    public function tipoProduto():BelongsTo {
        return $this->belongsTo(TipoProduto::class);
    }

    /**
     *  Relacionamento
     *  Um produto pode estar em vários itens de venda
     */
    public function itensVenda(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
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
