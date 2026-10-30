<?php

namespace Database\Seeders;

use App\Models\FormaPagamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormaPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formas = [
            'Dinheiro',
            'Pix',
            'Cartão de débito',
            'Cartão de crédito',
        ];

        foreach ($formas as $forma) {
            FormaPagamento::firstOrCreate([
                'nome' => $forma,
            ], [
                'status' => true,
            ]);
        }
    }
}
