<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PapelSeed::class);
        $this->call(UsuarioSeed::class);

        $this->clientes();
        $this->veiculos();
        $this->servicos();
        $this->pecasInsumos();
        $this->clienteVeiculos();
        $this->ordemDeServicos();
        $this->ordemDeServicoItems();
        $this->ordemDeServicoServicos();
    }

    public function clientes(): void
    {
        \App\Modules\Cliente\Model\Cliente::factory()->count(30)->create();
    }

    public function veiculos(): void
    {
        \App\Modules\Veiculo\Model\Veiculo::factory()->count(30)->create();
    }

    public function servicos(): void
    {
        \App\Modules\Servico\Model\Servico::factory()->count(30)->create();
    }

    public function pecasInsumos(): void
    {
        \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->count(30)->create();
    }

    public function clienteVeiculos(): void
    {
        \App\Modules\ClienteVeiculo\Model\ClienteVeiculo::factory()->count(30)->create();
    }

    public function ordemDeServicos(): void
    {
        \App\Modules\OrdemDeServico\Model\OrdemDeServico::factory()->count(30)->create();
    }

    public function ordemDeServicoItems(): void
    {
        \App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem::factory()->count(30)->create();
    }

    public function ordemDeServicoServicos(): void
    {
        \App\Modules\OrdemDeServicoServico\Model\OrdemDeServicoServico::factory()->count(30)->create();
    }
}
