<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'pessoa_id',
        'observacoes',
    ];

    /**
     * Relacionamento
     * Um cliente pertence a uma pessoa
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    protected static function booted()
    {
        static::deleted(function ($cliente) {
            $cliente->pessoa()->delete();
        });
    }
}
