<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    protected $fillable = [
        'pessoa_id',
        'data_admissao',
        'observacoes',
    ];

    /**
     * Relacionamento
     * Um funcionário pertence a uma pessoa
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    protected static function booted()
    {
        static::deleted(function ($funcionario) {
            $funcionario->pessoa()->delete();
        });
    }
}
