<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $fillable = [
        'status',
        'nome',
        'cpf',
        'cnpj',
        'tipo',
        'rg',
        'celular',
        'telefone',
        'email',
        'cep',
        'bairro',
        'logradouro',
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
}
