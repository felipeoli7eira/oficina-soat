<?php

namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Domain\Usuario\ProfileEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('usuarios')->insert([
            'uuid'   => \Illuminate\Support\Str::uuid(),
            'nome'   => 'SOAT ADMIN',
            'email'  => 'admin@oficinasoat.com.br',
            'senha'  => Hash::make('padrao'),
            'ativo'  => true,
            'perfil' => ProfileEnum::ADMIN->value,
        ]);

        DB::table('usuarios')->insert([
            'uuid'   => \Illuminate\Support\Str::uuid(),
            'nome'   => 'Mecânico',
            'email'  => 'mecanico@oficinasoat.com.br',
            'senha'  => Hash::make('padrao'),
            'ativo'  => true,
            'perfil' => ProfileEnum::TECNICO_AUTOMOTIVO->value,
        ]);

        DB::table('usuarios')->insert([
            'uuid'   => \Illuminate\Support\Str::uuid(),
            'nome'   => 'Atendente',
            'email'  => 'atendente@oficinasoat.com.br',
            'senha'  => Hash::make('padrao'),
            'ativo'  => true,
            'perfil' => ProfileEnum::ATENDENTE->value,
        ]);
    }
}
