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
