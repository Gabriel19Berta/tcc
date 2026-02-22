<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Pessoa extends Model
{
    protected $fillable = [
        'status',
        'nome',
        'cpf',
        'cnpj',
        'tipo',
        'rg',
        'ie',
        'celular',
        'telefone',
        'email',
        'cep',
        'bairro',
        'logradouro',
        'numero',
        'complemento',
        'cidade',
        'uf',
        'data_nascimento',
    ];

    protected $casts = [
        'status' => 'boolean',
        'data_nascimento' => 'date',
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    protected function statusFormatado(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status ? 'Ativo' : 'Inativo',
        );
    }
}
