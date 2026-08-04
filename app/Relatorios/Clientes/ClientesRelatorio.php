<?php

namespace App\Relatorios\Clientes;

use App\Models\Pessoa;
use App\Support\Formatador;
use App\Relatorios\Contratos\RelatorioInterface;
use Override;

class ClientesRelatorio implements RelatorioInterface
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
    public function linha($cliente): array
    {
        return [
            $cliente->id,
            Formatador::status($cliente->status),
            $cliente->nome,
            $cliente->cpf,
            $cliente->cnpj,
            $cliente->data_nascimento,
            $cliente->celular,
            $cliente->telefone,
            $cliente->email,
            $cliente->cep,
            $cliente->logradouro,
            $cliente->bairro,
            $cliente->numero,
            $cliente->complemento,
            $cliente->cidade,
            $cliente->uf,
            Formatador::data($cliente->created_at),
            Formatador::data($cliente->update_at)
        ];
    }

    public function titulo(): string
    {
        return 'Listagem De Clientes';
    }

    public function modulo(): string
    {
        return 'Clientes';
    }

    public function slug(): string
    {
        return 'clientes';
    }

    public function viewFiltros(): string
    {
        return 'relatorios.clientes.filtros';
    }

    public function gerar(array $filtros)
    {
        return Pessoa::with(['cliente'])
            ->whereHas('cliente')
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
