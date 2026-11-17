<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AtualizarItemVendaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantidade' => [
                'required',
                'numeric',
                'gt:0',
                'decimal:0,2',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'quantidade.required' => 'Informe a quantidade.',
            'quantidade.numeric' => 'A quantidade deve ser numérica.',
            'quantidade.gt' => 'A quantidade deve ser maior que zero.',
            'quantidade.decimal' => 'A quantidade deve ter no máximo duas casas decimais.',
        ];
    }
}
