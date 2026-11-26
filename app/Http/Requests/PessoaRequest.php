<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class PessoaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cpf' => $this->cpf ? preg_replace('/\D/', '', $this->cpf) : null,
            'cnpj' => $this->cnpj ? preg_replace('/[^A-Za-z0-9]/', '', $this->cnpj) : null,
            'rg'=> $this->rg ? preg_replace('/\D/', '', $this->rg) : null,
            'ie'=> $this->ie ? preg_replace('/\D/', '', $this->ie) : null,
            'celular' => $this->celular ? preg_replace('/\D/', '', $this->celular) : null,
            'telefone' => $this->telefone ? preg_replace('/\D/', '', $this->telefone) : null,
            'cep' => $this->cep ? preg_replace('/\D/', '', $this->cep) : null,
        ]);

        if (empty($this->cpf) && empty($this->cnpj)) {
            $this->merge([
                'tipo' => ''
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'boolean'],
            'nome' => ['required', 'string'],
            'tipo' => ['nullable', 'in:f,j'],
            'cpf' => ['nullable', 'string', 'size: 11', 'cpf', $this->documentoUnico('cpf')],
            'cnpj' => ['nullable', 'string', 'cnpj', $this->documentoUnico('cnpj')],
            'rg' => ['nullable', 'string'],
            'ie' => ['nullable', 'string'],
            'data_nascimento' => ['nullable', 'date'],

            'celular' => ['nullable', 'string', 'size: 11'],
            'telefone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],

            'cep' => ['nullable', 'string', 'size: 8'],
            'logradouro' => ['nullable', 'string'],
            'numero' => ['nullable', 'string'],
            'complemento' => ['nullable', 'string'],
            'bairro' => ['nullable', 'string'],
            'cidade' => ['nullable', 'string'],
            'uf' => ['nullable', 'string', 'size:2'],

            'observacoes' => ['nullable', 'string'],

            /* PARA FUNCIONÁRIO */
            'data_admissao' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Nome é obrigatório!',
            'cpf.size' => 'CPF deve ter 11 números!',
            'cpf.unique' => 'Já existe um cadastro com este CPF!',
            'cnpj.unique' => 'Já existe um cadastro com este CNPJ!',
            'celular.size' => 'Celular deve ter 11 números!',
            'cep.size' => 'CEP deve ter 8 números!'
        ];
    }

    private function documentoUnico(string $campo): Unique
    {
        $tabela = $this->routeIs('funcionarios.*') ? 'funcionarios' : 'clientes';

        return Rule::unique('pessoas', $campo)
            ->ignore($this->route('id'))
            ->where(fn ($query) => $query->whereIn('id', DB::table($tabela)->select('pessoa_id')));
    }

    
}