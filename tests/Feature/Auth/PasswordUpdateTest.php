<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated without the current password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
});

test('administrator can update own password and log in with it', function () {
    $user = User::factory()->create();
    $user->forceFill(['admin' => true])->save();

    $this->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors();

    $this->post(route('logout'));

    $this->post('/', [
        'email' => $user->email,
        'password' => 'new-password',
    ]);

    $this->assertAuthenticatedAs($user);
});

test('password confirmation is required to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'password' => 'new-password',
            'password_confirmation' => 'other-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'password')
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('password', $user->refresh()->password));
});
