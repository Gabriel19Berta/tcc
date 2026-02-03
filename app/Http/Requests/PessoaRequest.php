<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'cnpj' => $this->cnpj ? preg_replace('/\D/', '', $this->cnpj) : null,
            'celular' => $this->celular ? preg_replace('/\D/', '', $this->celular) : null,
            'telefone' => $this->telefone ? preg_replace('/\D/', '', $this->telefone) : null,
            'cep' => $this->cep ? preg_replace('/\D/', '', $this->cep) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string'],

            'tipo' => ['nullable', 'in:f,j'],
            'cpf' => ['nullable', 'string'],
            'cnpj' => ['nullable', 'string'],
            'rg' => ['nullable', 'string'],
            'data_nascimento' => ['nullable', 'date'],

            'celular' => ['nullable', 'string'],
            'telefone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],

            'cep' => ['nullable', 'string'],
            'logradouro' => ['nullable', 'string'],
            'numero' => ['nullable', 'string'],
            'complemento' => ['nullable', 'string'],
            'bairro' => ['nullable', 'string'],
            'cidade' => ['nullable', 'string'],
            'uf' => ['nullable', 'string', 'size:2'],

            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Nome é obrigatório!'
        ];
    }
}