<?php

use App\Modules\OrdemDeServico\Enums\StatusOrdemDeServico;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->uuid('uuid')->default(DB::raw('uuid_generate_v4()'))->unique();

            $table->timestamp('data_abertura')->useCurrent();
            $table->timestamp('data_finalizacao')->nullable();

            $table->integer('prazo_validade');

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('cliente')->onDelete('cascade');

            $table->unsignedBigInteger('veiculo_id');
            $table->foreign('veiculo_id')->references('id')->on('veiculo')->onDelete('cascade');

            $table->string('descricao');
            $table->decimal('valor_desconto', 15, 2);
            $table->decimal('valor_total', 15, 2);
            $table->string('status')->default(StatusOrdemDeServico::RECEBIDA);

            $table->unsignedBigInteger('usuario_id_atendente');
            $table->foreign('usuario_id_atendente')->references('id')->on('usuario');

            $table->unsignedBigInteger('usuario_id_mecanico');
            $table->foreign('usuario_id_mecanico')->references('id')->on('usuario');

            $table->boolean('excluido')->default(false);
            $table->timestamp('data_exclusao')->nullable();

            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_atualizacao')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('os');
    }
};
