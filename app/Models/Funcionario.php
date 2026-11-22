<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    protected $fillable = [
        'pessoa_id',
        'data_admissao',
        'observacoes',
        'user_id',
    ];

    /**
     * Relacionamento
     * Um funcionário pertence a uma pessoa
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    /**
     * Relacionamento
     * Um funcionário pode possuir uma conta de acesso.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento
     * Um funcionário pode ser o responsável por várias vendas
     */
    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    protected static function booted()
    {
        static::deleted(function ($funcionario) {
            $funcionario->pessoa()->delete();
        });
    }
}
