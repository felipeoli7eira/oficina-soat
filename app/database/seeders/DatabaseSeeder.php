<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PapelSeed::class);

        // $this->roles();
        $this->clientes();
        $this->veiculos();
        $this->servicos();
        $this->pecasInsumos();
        $this->clienteVeiculos();
    }

    public function roles(): void
    {
        DB::table('roles')->insert(['name' => 'atendente', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'comercial', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'mecanico', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'gestor_estoque', 'guard_name' => 'web']);
    }

    public function clientes(): void
    {
        \App\Modules\Cliente\Model\Cliente::factory()->count(10)->create();
    }

    public function veiculos(): void
    {
        \App\Modules\Veiculo\Model\Veiculo::factory()->count(50)->create();
    }

    public function servicos(): void
    {
        \App\Modules\Servico\Model\Servico::factory()->count(10)->create();
    }

    public function pecasInsumos(): void
    {
        \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->count(10)->create();
    }

    public function clienteVeiculos(): void
    {
        \App\Modules\ClienteVeiculo\Model\ClienteVeiculo::factory()->count(10)->create();
    }
}
