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

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? 'Ativo' : 'Inativo',
        );
    }

    protected function cpf(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $value)
                : null
        );
    }

    protected function cnpj(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $value)
                : null
        );
    }

    protected function celular(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $value)
                : null
        );
    }
}
