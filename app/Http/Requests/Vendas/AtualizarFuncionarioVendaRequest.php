<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtualizarFuncionarioVendaRequest extends FormRequest
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
            'funcionario_id' => self::regraFuncionario(),
        ];
    }

    public function messages(): array
    {
        return self::mensagensFuncionario();
    }

    /**
     * Regra compartilhada com AdicionarProdutoRequest:
     * opcional, mas, se informado, deve ser um funcionário existente e ativo.
     */
    public static function regraFuncionario(): array
    {
        return [
            'nullable',
            'integer',
            Rule::exists('funcionarios', 'id')->where(function ($query) {
                $query->whereIn('pessoa_id', function ($pessoas) {
                    $pessoas->select('id')->from('pessoas')->where('status', true);
                });
            }),
        ];
    }

    public static function mensagensFuncionario(): array
    {
        return [
            'funcionario_id.integer' => 'O funcionário selecionado é inválido.',
            'funcionario_id.exists' => 'O funcionário selecionado não existe ou está inativo.',
        ];
    }
}
