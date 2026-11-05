<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AdicionarProdutoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected  function prepareForValidation(): void
    {
        $preco = $this->input('preco_produto');

        if ($preco !== null) {
            $preco = trim(str_replace(['R$', ' '], '', $preco));

            // Quando houver vírgula, considera o formato brasileiro.
            if (str_contains($preco, ',')) {
                $preco = str_replace('.', '', $preco);
                $preco = str_replace(',', '.', $preco);
            }
            $this->merge([
                'preco_produto' => $preco,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'produto_id' => [
                'required',
                'integer',
                'exists:produtos,id',
            ],
            'quantidade' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'preco_produto' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'venda_id' => [
                'nullable',
                'integer',
                'exists:vendas,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'produto_id.required' => 'Selecione um produto.',
            'produto_id.exists' => 'O produto selecionado não existe.',
            'quantidade.required' => 'Informe a quantidade.',
            'quantidade.numeric' => 'A quantidade deve ser numérica.',
            'quantidade.gt' => 'A quantidade deve ser maior que zero.',
            'quantidade.decimal' => 'A quantidade pode ter no máximo duas casas decimais.',
            'preco_produto.required' => 'Informe o preço do produto.',
            'preco_produto.numeric' => 'O preço deve ser numérico.',
            'preco_produto.gt' => 'O preço deve ser maior que zero.',
            'preco_produto.decimal' => 'O preço pode ter no máximo duas casas decimais.',
            'venda_id.exists' => 'A venda selecionada não existe.',
        ];
    }
}
