<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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

    public function funcionario()
    {
        return $this->hasOne(Funcionario::class);
    }

    protected function statusFormatada(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['status'] ? 'Ativo' : 'Inativo',
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
                ? preg_replace('/^([A-Z0-9]{2})([A-Z0-9]{3})([A-Z0-9]{3})([A-Z0-9]{4})(\d{2})$/', '$1.$2.$3/$4-$5', $value)
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

    protected function telefone(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $value)
                : null
        );
    }

    protected function cep(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value
                ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $value)
                : null
        );
    }

    protected function dataNascimentoFormatada(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['data_nascimento']
                ? Carbon::parse($attributes['data_nascimento'])->format('d/m/Y')
                : null
        );
    }

    /**
     * Filtros de pessoa
     */
    public function scopeFiltroCodigo($query, $codigo, $relacao)
    {
        if ($codigo) {
            $query->whereRelation($relacao, 'id', $codigo);
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
        if (!$nome) {
            return $query;
        }

        $termos = preg_split('/\s+/', trim($nome));

        return $query->where(function ($q) use ($termos) {

            foreach ($termos as $termo) {

                if (empty($termo)) {
                    continue;
                }

                $numero = preg_replace('/[^A-Za-z0-9]/', '', $termo);

                $q->where(function ($sub) use ($termo, $numero) {

                    $sub->where('nome', 'like', "%{$termo}%");

                    if ($numero) {
                        $sub->orWhere('cpf', 'like', "%{$numero}%")
                            ->orWhere('cnpj', 'like', "%{$numero}%");
                    }
                });
            }
        });
    }

    public function scopeFiltroTipo($query, $tipo) 
    {
        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        return $query;
    }
}
