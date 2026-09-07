<?php

namespace App\Relatorios\Funcionarios;

use App\Models\Pessoa;
use App\Support\Formatador;
use App\Relatorios\Contratos\RelatorioInterface;
use Override;

class FuncionariosRelatorio implements RelatorioInterface
{

#[Override]
    public function cabecalhos(): array
    {
        return [
            'Código',
            'Status',
            'Nome',
            'CPF',
            'CNPJ',
            'Celular',
            'Telefone',
            'Email',
            'CEP',
            'Logradouro',
            'Bairro',
            'Número',
            'Complemento',
            'Cidade',
            'UF',
            'Criado em',
            'Modificado em'
        ];
    }

    #[Override]
    public function linha($funcionario): array
    {
        return [
            $funcionario->id,
            Formatador::status($funcionario->status),
            $funcionario->nome,
            $funcionario->cpf,
            $funcionario->cnpj,
            $funcionario->data_nascimento,
            $funcionario->celular,
            $funcionario->telefone,
            $funcionario->email,
            $funcionario->cep,
            $funcionario->logradouro,
            $funcionario->bairro,
            $funcionario->numero,
            $funcionario->complemento,
            $funcionario->cidade,
            $funcionario->uf,
            Formatador::data($funcionario->created_at),
            Formatador::data($funcionario->update_at)
        ];
    }

    public function titulo(): string
    {
        return 'Listagem De Funcionarios';
    }

    public function modulo(): string
    {
        return 'Funcionarios';
    }

    public function slug(): string
    {
        return 'Funcionarios';
    }

    public function viewFiltros(): string
    {
        return 'relatorios.Funcionarios.filtros';
    }

    public function gerar(array $filtros)
    {
        return Pessoa::with(['funcionario'])
            ->whereHas('funcionario')
            ->when(filled($filtros['status']),
                fn($query) => $query->where(
                    'status',
                    '=',
                    $filtros['status']
                )
            )
            ->when(!empty($filtros['data-inicio']),
                fn($query) => $query->whereDate(
                    'created_at',
                    '>=',
                    $filtros['data-inicio']
                )
            )
            ->when(!empty($filtros['data-fim']),
                fn($query) => $query->whereDate(
                    'updated_at',
                    '<=',
                    $filtros['data-fim']
                )
            )
            ->orderBy('nome')
            ->get();
    }
}
