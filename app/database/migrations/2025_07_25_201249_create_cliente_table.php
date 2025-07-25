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
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('nome', 100);
            $table->string('cpf_cnpj', 20);
            $table->string('email', 50);
            $table->string('fone', 20);
            $table->string('cep', 20);
            $table->string('rua', 100);
            $table->string('numero', 20);
            $table->string('cidade', 50);
            $table->string('uf', 2);
            $table->string('complemento', 100);

            $table->boolean('excluido')->default(false);
            $table->timestamp('data_exclusao');

            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
