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
        $this->statusDisponiveis();
    }

    public function roles(): void
    {
        DB::table('roles')->insert(['name' => 'atendente', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'comercial', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'mecanico', 'guard_name' => 'web']);
        DB::table('roles')->insert(['name' => 'gestor_estoque', 'guard_name' => 'web']);
        $this->pecasInsumos();
        $this->clienteVeiculos();
        $this->ordemDeServicos();
        $this->ordemDeServicoItems();
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

    public function statusDisponiveis(): void
    {
        $status = [
            ['descricao' => 'Recebida',                    'ordem' => 1],
            ['descricao' => 'Em Diagnóstico',              'ordem' => 2],
            ['descricao' => 'Recusado pelo Cliente',       'ordem' => 3],
            ['descricao' => 'Desistência do Cliente',      'ordem' => 4],
            ['descricao' => 'Expirado',                    'ordem' => 5],
            ['descricao' => 'Aguardando Aprovação',        'ordem' => 6],
            ['descricao' => 'Aprovado',                    'ordem' => 7],
            ['descricao' => 'Aguardando Peças/Insumos',    'ordem' => 8],
            ['descricao' => 'Peças/Insumos Disponíveis',   'ordem' => 9],
            ['descricao' => 'Peças/Insumos Indisponíveis', 'ordem' => 10],
            ['descricao' => 'Em Execução',                 'ordem' => 11],
            ['descricao' => 'Finalizada',                  'ordem' => 12],
            ['descricao' => 'Entregue',                    'ordem' => 13],
        ];

        foreach ($status as $st) {
            \App\Modules\StatusDisponiveis\Model\StatusDisponiveis::factory()->create($st);
        }
    }
}
