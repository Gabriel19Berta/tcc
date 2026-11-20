<?php

namespace App\Models;

use App\Support\Formatador;
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

    public function scopeFiltroBusca($query, ?string $busca)
    {
        if ($busca && preg_match('/^pessoa:(\d+)$/', $busca, $partes)) {
            return $query->whereKey($partes[1]);
        }

        return $query->filtroNome($busca);
    }

    /**
     * Texto exibido nas sugestões de busca: "Nome — documento (Inativo)".
     */
    public function descricaoBusca(): string
    {
        $documento = Formatador::cpf($this->cpf) ?: Formatador::cnpj($this->cnpj);

        return $this->nome
            . ($documento ? " — {$documento}" : '')
            . ($this->status ? '' : ' (Inativo)');
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
