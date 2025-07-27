<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(DB::raw('uuid_generate_v4()'))->unique();
            $table->string('nome', 100);
            $table->string('cpf', 11)->nullable();
            $table->string('cnpj', 14)->nullable();
            $table->string('email', 50);
            $table->string('telefone_movel', 20);

            $table->string('cep', 8);
            $table->string('logradouro', 100);
            $table->string('numero', 20)->nullable();
            $table->string('bairro', 50);
            $table->string('complemento', 100)->nullable();
            $table->string('cidade', 50);
            $table->string('uf', 2);

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
        Schema::dropIfExists('cliente');
    }
};
