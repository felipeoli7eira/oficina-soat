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
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('nome')->unique();
            $table->unsignedBigInteger('valor'); // inteiro para representar os valores em centavos
            $defaultsTimestampsColumns = require __DIR__ . '/../defaults/ColumnsTimestamps.php';
            $defaultsTimestampsColumns->addDefaultColumnsTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
