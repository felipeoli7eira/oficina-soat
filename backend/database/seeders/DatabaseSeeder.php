<?php

namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Domain\Entity\Usuario\Perfil;
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

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('usuarios')->insert([
            'uuid'   => \Illuminate\Support\Str::uuid(),
            'nome'   => 'SOAT',
            'email'  => 'soat@example.com',
            'senha'  => Hash::make('padrao'),
            'ativo'  => true,
            'perfil' => Perfil::MECANICO->value,
        ]);
    }
}
