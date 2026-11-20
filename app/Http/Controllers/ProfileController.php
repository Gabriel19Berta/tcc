<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Exibe o formulário de perfil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Atualiza as informações do próprio usuário.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $usuario = $request->user();

        $usuario->fill($request->validated());

        if ($usuario->isDirty('email')) {
            $usuario->email_verified_at = null;
        }

        $usuario->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }
}
