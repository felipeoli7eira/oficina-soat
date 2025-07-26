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
        Schema::create('os_status', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            $table->unsignedBigInteger('status_disponiveis_id');
            $table->foreign('status_disponiveis_id')->references('id')->on('status_disponiveis')->onDelete('cascade');

            $table->unsignedBigInteger('os_id');
            $table->foreign('os_id')->references('id')->on('os')->onDelete('cascade');

            $table->timestamp('data_cadastro')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('os_status');
    }
};
