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
            $table->uuid()->unique();
            $table->string('descricao', 100);
            $table->decimal('valor', 15, 2);
            $table->string('status', 30);

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
        Schema::dropIfExists('servicos');
    }
};
