<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
}
