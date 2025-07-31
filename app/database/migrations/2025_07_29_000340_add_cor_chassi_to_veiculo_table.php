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
        Schema::table('veiculo', function (Blueprint $table) {
            $table->string('cor', 30)->nullable()->after('ano_fabricacao');
            $table->string('chassi', 50)->unique()->after('cor');

            // Corrigir data_exclusao para ser nullable
            $table->timestamp('data_exclusao')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('veiculo', function (Blueprint $table) {
            $table->dropColumn(['cor', 'chassi']);

            // Reverter data_exclusao para not null
            $table->timestamp('data_exclusao')->nullable(false)->change();
        });
    }
};
