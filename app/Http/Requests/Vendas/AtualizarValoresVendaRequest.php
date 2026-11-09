<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AtualizarValoresVendaRequest extends FormRequest
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
        $regrasDesconto = [
            'numeric',
            'min:0',
            'decimal:0,2',
        ];

        if ($this->input('tipo_desconto') === 'percentual') {
            $regrasDesconto[] = 'max:100';
        }

        return [
            'tipo_desconto' => [
                'required',
                'in:valor,percentual',
            ],
            'desconto' => $regrasDesconto,
            'outros_custos' => [
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $dados = [];

        foreach (['desconto', 'outros_custos'] as $campo) {
            $valor = $this->input($campo);

            if ($valor === null || trim((string) $valor) === '') {
                $dados[$campo] = 0;
                continue;
            }

            $valor = trim(str_replace(['R$', ' '], '', (string) $valor));

            if ($valor !== null) {
                $valor = trim(str_replace(['R$', ' '], '', $valor));

                if (str_contains($valor, ',')) {
                    $valor = str_replace('.', '', $valor);
                    $valor = str_replace(',', '.', $valor);
                }

                $dados[$campo] = $valor;
            }
        }

        $this->merge($dados);
    }

    public function messages(): array
    {
        return [
            'tipo_desconto.required' => 'Selecione o tipo de desconto.',
            'tipo_desconto.in' => 'O tipo de desconto selecionado é inválido.',
            'desconto.numeric' => 'O desconto deve ser numérico.',
            'desconto.min' => 'O desconto não pode ser negativo.',
            'desconto.decimal' => 'O desconto pode ter no máximo duas casas decimais.',
            'desconto.max' => 'O desconto percentual não pode ultrapassar 100%.',
            'outros_custos.numeric' => 'O acréscimo deve ser numérico.',
            'outros_custos.min' => 'O acréscimo não pode ser negativo.',
            'outros_custos.decimal' => 'O acréscimo pode ter no máximo duas casas decimais.',
        ];
    }
}
