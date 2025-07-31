<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->papeis();
        $this->clientes();
        $this->veiculos();
    }

    public function papeis(): void
    {
        DB::table('roles')->insert(['name' => 'atendente', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'comercial', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'mecanico', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'gestor_estoque', 'guard_name' => 'web']);
    }

    public function clientes(): void
    {
        \App\Modules\Cliente\Model\Cliente::factory()->count(50)->create();
    }

    public function veiculos(): void
    {
        \App\Modules\Veiculo\Model\Veiculo::factory()->count(50)->create();
    }
}
