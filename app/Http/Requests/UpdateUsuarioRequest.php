<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
                        $query->whereNull('user_id')
                            ->orWhere('user_id', $usuario->id);
                    }),
            ],
        ];
    }
}
