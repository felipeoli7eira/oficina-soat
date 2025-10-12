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
        Schema::create('os', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->foreignId('cliente_id')->references('id')->on('clientes')->onDelete('CASCADE');
            $table->foreignId('veiculo_id')->references('id')->on('veiculos')->onDelete('CASCADE');

            $table->string('descricao')->nullable();

            $table->string('status');

            $table->timestamp('dt_abertura');
            $table->timestamp('dt_finalizacao')->nullable();
            $table->timestamp('dt_atualizacao')->nullable()->useCurrentOnUpdate();

            // $defaultsTimestampsColumns = require __DIR__ . '/../defaults/ColumnsTimestamps.php';
            // $defaultsTimestampsColumns->addDefaultColumnsTimestamps($table);
        });

        Schema::create('os_servico', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->foreignId('os_id')->references('id')->on('os')->onDelete('CASCADE');
            $table->foreignId('servico_id')->references('id')->on('servicos')->onDelete('CASCADE');

            $defaultsTimestampsColumns = require __DIR__ . '/../defaults/ColumnsTimestamps.php';
            $defaultsTimestampsColumns->addDefaultColumnsTimestamps($table);
        });

        Schema::create('os_material', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->foreignId('os_id')->references('id')->on('os')->onDelete('CASCADE');
            $table->foreignId('material_id')->references('id')->on('materiais')->onDelete('CASCADE');

            $defaultsTimestampsColumns = require __DIR__ . '/../defaults/ColumnsTimestamps.php';
            $defaultsTimestampsColumns->addDefaultColumnsTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('os_material');
        Schema::dropIfExists('os_servico');
        Schema::dropIfExists('os');
    }
};
