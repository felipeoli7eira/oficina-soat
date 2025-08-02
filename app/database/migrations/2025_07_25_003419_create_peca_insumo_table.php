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
        Schema::create('peca_insumo', function (Blueprint $table) {
            $table->id();

            $table->string('gtin', 20);
            $table->string('descricao', 100);
            $table->decimal('valor_custo', 15, 2);
            $table->decimal('valor_venda', 15, 2);
            $table->integer('qtd_atual');
            $table->integer('qtd_segregada');

            $table->string('status', 30);

            $table->boolean('excluido')->default(false);
            $table->timestamp('data_exclusao')->nullable();

            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_atualizacao')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peca_insumo');
    }
};
