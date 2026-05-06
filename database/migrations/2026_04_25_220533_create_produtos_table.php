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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->constrained()->nullable();
            $table->foreignId('tipo_produto_id')->constrained()->nullable();
            $table->boolean('status')->default(true);
            $table->string('nome');
            $table->decimal('preco_venda', 10, 2)->default(0)->nullable();
            $table->decimal('preco_custo', 10, 2)->default(0)->nullable();
            $table->boolean('controla_estoque')->default(true);
            $table->decimal('quantidade', 10, 2)->nullable();
            $table->decimal('peso', 10, 2)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
