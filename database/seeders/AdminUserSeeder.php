<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $nome = env('ADMIN_USER_NAME', 'Administrador');
        $email = env('ADMIN_USER_EMAIL');
        $senha = env('ADMIN_USER_PASSWORD');

        if (!$email || !$senha) {
            throw new RuntimeException(
                'Configure ADMIN_USER_EMAIL e ADMIN_USER_PASSWORD no arquivo .env.'
            );
        }

        if (strlen($senha) < 12) {
            throw new RuntimeException(
                'A senha do administrador deve possuir pelo menos 12 caracteres.'
            );
        }

        $usuario = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $nome,
                'password' => $senha,
            ]
        );

        $usuario->forceFill([
            'admin' => true,
        ])->save();
    }
}
