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
        Schema::create('materiais', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('nome')->unique();
            $table->string('gtin', 20)->unique();
            $table->string('sku', 255)->nullable()->unique();
            $table->string('descricao')->nullable();
            $table->integer('estoque');
            $table->unsignedBigInteger('preco_custo'); // para comprar de um fornecedor
            $table->unsignedBigInteger('preco_venda'); // para vender na oficina
            $table->unsignedBigInteger('preco_uso_interno'); // para usar ela em uma os

            $defaultsTimestampsColumns = require __DIR__ . '/../defaults/ColumnsTimestamps.php';
            $defaultsTimestampsColumns->addDefaultColumnsTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiais');
    }
};
