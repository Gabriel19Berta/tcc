<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estorno de um recebimento: devolve o total já recebido, preservando os
     * pagamentos originais. Um recebimento pode ser estornado uma única vez.
     */
    public function up(): void
    {
        Schema::create('estornos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recebimento_id')->unique()->constrained();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('valor', 10, 2);
            $table->dateTime('data_estorno');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estornos');
    }
};
