<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Movimentações de pagamento de um recebimento (permite pagamentos parciais).
     */
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recebimento_id')->constrained();
            $table->foreignId('forma_pagamento_id')->constrained();
            $table->decimal('valor', 10, 2);
            $table->dateTime('data_pagamento');
            $table->timestamps();
        });

        // Recebimentos já pagos antes desta estrutura: registra o pagamento integral,
        // para que o total recebido e o saldo fiquem coerentes com o status.
        $agora = now();

        DB::table('recebimentos')
            ->where('status', 'pago')
            ->orderBy('id')
            ->get()
            ->each(function ($recebimento) use ($agora) {
                DB::table('pagamentos')->insert([
                    'recebimento_id' => $recebimento->id,
                    'forma_pagamento_id' => $recebimento->forma_pagamento_id,
                    'valor' => $recebimento->valor,
                    'data_pagamento' => $recebimento->data_pagamento ?? $recebimento->updated_at ?? $agora,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
