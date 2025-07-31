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
        Schema::create('veiculo', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('placa', 20)->unique();
            $table->string('modelo', 50);
            $table->string('marca', 50);
            $table->year('ano_fabricacao');

            $table->boolean('excluido')->default(false);
            $table->timestamp('data_exclusao');

            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_atualizacao')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculo');
    }
};
