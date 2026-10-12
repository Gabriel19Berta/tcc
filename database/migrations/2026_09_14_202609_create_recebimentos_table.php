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
        Schema::create('recebimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venda_id')->constrained();
            $table->foreignId('forma_pagamento_id')->constrained();
            $table->decimal('valor', 10, 2);
            $table->string('descricao');
            $table->dateTime('data_vencimento');
            $table->dateTime('data_pagamento')->nullable();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recebimentos');
    }
};
