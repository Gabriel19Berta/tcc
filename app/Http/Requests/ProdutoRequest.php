<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'preco_custo' => $this->converterValor($this->preco_custo),
            'preco_venda' => $this->converterValor($this->preco_venda)
        ]);
    }

    private function converterValor(?string $valor): float
    {
        if(!$valor) {
            return 0.00;
        }

        return (float) str_replace(',', '.', str_replace('.', '', $valor));
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
            'marca_id' => ['nullable', 'integer'],
            'tipo_produto_id' => ['nullable', 'integer'],
            'peso' => ['nullable', 'numeric'],
            'preco_custo' => ['nullable', 'numeric'],
            'preco_venda' => ['nullable ', 'numeric', 'gte:preco_custo'],
            'controla_estoque' => ['nullable', 'boolean'],
            'quantidade' => ['required_if:controla_estoque, 1', 'nullable', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Nome é obrigatório!',
            'quantidade.required' => 'Quantidade é obrigatório!',
            'preco_venda.gte' => 'Preço de venda deve ser maior ou igual ao preço de custo!'
        ];
    }
}
