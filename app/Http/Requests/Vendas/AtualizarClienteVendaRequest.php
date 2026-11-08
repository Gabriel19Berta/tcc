<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AtualizarClienteVendaRequest extends FormRequest
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
            'cliente_id' => [
                'nullable',
                'integer',
                'exists:clientes,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.integer' => 'O cliente selecionado é inválido.',
            'cliente_id.exists' => 'O cliente selecionado não existe.',
        ];
    }
}
