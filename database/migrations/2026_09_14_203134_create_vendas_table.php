<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained();
            $table->foreignId('funcionario_id')->nullable()->constrained();
            $table->string('status')->default('aberta');
            $table->decimal('valor_produtos', 10, 2);
            $table->decimal('descontos', 10, 2)->default(0);
            $table->decimal('outros_custos', 10, 2)->default(0);
            $table->decimal('valor_total', 10, 2);
            $table->text('justificativa_cancelamento')->nullable();
            $table->dateTime('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
