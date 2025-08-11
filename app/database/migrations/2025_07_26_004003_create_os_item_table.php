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
        Schema::create('os_item', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(DB::raw('uuid_generate_v4()'))->unique();

            $table->unsignedBigInteger('peca_insumo_id');
            $table->foreign('peca_insumo_id')->references('id')->on('peca_insumo')->onDelete('cascade');

            $table->unsignedBigInteger('os_id');
            $table->foreign('os_id')->references('id')->on('os')->onDelete('cascade');

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
        Schema::dropIfExists('os_item');
    }
};
