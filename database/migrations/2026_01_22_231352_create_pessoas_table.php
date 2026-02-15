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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->string('nome');
            $table->char('cpf', 11)->nullable();
            $table->char('cnpj', 14)->nullable();
            $table->char('tipo', 1)->nullable();
            $table->string('rg')->nullable();
            $table->string('celular')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->char('cep', 8)->nullable();
            $table->string('bairro')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('complemento')->nullable();
            $table->string('cidade')->nullable();
            $table->char('uf', 2);
            $table->date('data_nascimento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
