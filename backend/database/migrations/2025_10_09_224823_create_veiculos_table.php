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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('cliente_id')->references('id')->on('clientes')->onDelete('CASCADE');
            $table->string('marca');
            $table->string('modelo');
            $table->string('placa')->unique();
            $table->year('ano');


            $defaultsTimestampsColumns = require __DIR__ . '/../defaults/ColumnsTimestamps.php';
            $defaultsTimestampsColumns->addDefaultColumnsTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
