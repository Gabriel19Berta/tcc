<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdicionarRecebimentoRequest extends FormRequest
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
        $valor = $this->input('valor');

        if ($valor !== null) {
            $valor = trim(str_replace(['R$', ' '], '', (string) $valor));

            if (str_contains($valor, ',')) {
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            }

            $this->merge(['valor' => $valor]);
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
            'forma_pagamento_id' => [
                'required',
                'integer',
                Rule::exists('forma_pagamentos', 'id')
                    ->where('status', true),
            ],
            'valor' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
            'status' => [
                'required',
                'in:pago,pendente',
            ],
            'descricao' => [
                'nullable',
                'string',
                'max:255',
            ],
            'data_vencimento' => [
                'required_if:status,pendente',
                'nullable',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'forma_pagamento_id.required' => 'Selecione uma forma de pagamento.',
            'forma_pagamento_id.exists' => 'A forma de pagamento selecionada não está disponível.',
            'valor.required' => 'Informe o valor do recebimento.',
            'valor.numeric' => 'Informe um valor válido.',
            'valor.gt' => 'O valor deve ser maior que zero.',
            'valor.decimal' => 'Informe o valor com até duas casas decimais.',
            'status.required' => 'Informe a situação do recebimento.',
            'status.in' => 'A situação selecionada é inválida.',
            'data_vencimento.required_if' => 'Informe a data de vencimento para recebimentos pendentes.',
            'data_vencimento.date' => 'Informe uma data de vencimento válida.',
        ];
    }
}
