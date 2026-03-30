<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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

    protected function dataAdmissaoFormatada(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['data_admissao']
                ? Carbon::parse($attributes['data_admissao'])->format('d/m/Y')
                : null
        );
    }
}
