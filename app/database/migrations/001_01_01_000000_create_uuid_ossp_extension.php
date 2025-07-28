<?php

use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    public function up(): void
    {
        if (env('DB_CONNECTION') === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        }
    }

    public function down(): void
    {
    }
};
