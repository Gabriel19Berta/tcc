<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUsuarioRequest extends FormRequest
{
    /**
     * Somente administradores podem cadastrar contas de acesso.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->admin;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                Password::defaults(),
                'confirmed',
            ],

            'admin' => ['required', 'boolean'],

            'funcionario_id' => [
                'nullable',
                'integer',
                Rule::exists('funcionarios', 'id')
                    ->where(fn ($query) => $query->whereNull('user_id')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'funcionario_id.exists' => 'O funcionário selecionado não existe ou já possui uma conta vinculada.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'password' => 'senha',
            'admin' => 'tipo de acesso',
            'funcionario_id' => 'funcionário',
        ];
    }
}
