<?php

use Illuminate\Database\Schema\Blueprint;

return new class {
    public function addDefaultColumnsTimestamps(Blueprint $table)
    {
        $table->timestamp('criado_em')->useCurrent();
        $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        $table->timestamp('deletado_em')->nullable();
    }
};
