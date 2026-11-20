<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUsuarioRequest extends FormRequest
{
    /**
     * Somente administradores podem editar contas de acesso.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->admin;
    }

    public function rules(): array
    {
        /** @var User $usuario */
        $usuario = $this->route('usuario');

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],

            // Opcional na edição: se vazio, a senha atual é mantida.
            'password' => [
                'nullable',
                'string',
                Password::defaults(),
                'confirmed',
            ],

            'admin' => ['required', 'boolean'],

            'funcionario_id' => [
                'nullable',
                'integer',
                Rule::exists('funcionarios', 'id')
                    ->where(function ($query) use ($usuario) {
                        $query->where(function ($q) use ($usuario) {
                            $q->whereNull('user_id')
                                ->orWhere('user_id', $usuario->id);
                        });
                    }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'funcionario_id.exists' => 'O funcionário selecionado não existe ou já possui outra conta vinculada.',
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
