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
        Schema::create('os_servico', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(DB::raw('uuid_generate_v4()'))->unique();

            $table->unsignedBigInteger('os_id');
            $table->foreign('os_id')->references('id')->on('os')->onDelete('cascade');

            $table->unsignedBigInteger('servico_id');
            $table->foreign('servico_id')->references('id')->on('servicos')->onDelete('cascade');

            $table->string('observacao');
            $table->integer('quantidade');
            $table->decimal('valor', 15, 2);

            $table->boolean('excluido')->default(false);
            $table->timestamp('data_exclusao')->nullable();

            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('os_servico');
    }
};
