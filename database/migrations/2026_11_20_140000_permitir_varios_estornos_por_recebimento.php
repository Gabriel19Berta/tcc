<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * O estorno passa a ser uma movimentação histórica (não encerra o recebimento):
     * - um recebimento pode ter vários estornos (um por ciclo de pagamentos);
     * - cada pagamento indica o estorno que o desfez (null = ainda válido);
     * - recebimentos marcados como "estornado" voltam a "pendente".
     */
    public function up(): void
    {
        // Remove o índice único (a chave estrangeira é recriada com índice comum).
        Schema::table('estornos', function (Blueprint $table) {
            $table->dropForeign(['recebimento_id']);
            $table->dropUnique(['recebimento_id']);
            $table->foreign('recebimento_id')->references('id')->on('recebimentos');
        });

        Schema::table('pagamentos', function (Blueprint $table) {
            $table->foreignId('estorno_id')->nullable()->after('forma_pagamento_id')->constrained('estornos');
        });

        // Dados existentes: antes, cada recebimento tinha no máximo um estorno e não
        // recebia pagamentos depois dele; logo, todos os seus pagamentos foram estornados.
        DB::table('estornos')->orderBy('id')->get()->each(function ($estorno) {
            DB::table('pagamentos')
                ->where('recebimento_id', $estorno->recebimento_id)
                ->whereNull('estorno_id')
                ->update(['estorno_id' => $estorno->id]);
        });

        DB::table('recebimentos')
            ->where('status', 'estornado')
            ->update(['status' => 'pendente', 'data_pagamento' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('estorno_id');
        });

        Schema::table('estornos', function (Blueprint $table) {
            $table->dropForeign(['recebimento_id']);
            $table->unique('recebimento_id');
            $table->foreign('recebimento_id')->references('id')->on('recebimentos');
        });
    }
};
