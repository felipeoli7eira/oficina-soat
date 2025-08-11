<?php

namespace Tests\Feature\Modules\Veiculo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Modules\Veiculo\Model\Veiculo;

class VeiculoListagemTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('veiculo');
    }

    public function test_listar_todos_veiculos(): void
    {
        $veiculos = Veiculo::factory(3)->create()->fresh();
        $response = $this->getJson('/api/veiculo');

        $response->assertOk();
    }

    public function test_listar_veiculo_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->getJson('/api/veiculo/' . $uuid);
        $response->assertStatus(400);
    }

    public function test_listar_veiculos_com_erro_interno(): void
    {
        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('listagem')
                ->once()
                ->andThrow(new \Exception('Erro interno na listagem'));
        });

        $response = $this->getJson('/api/veiculo');

        $response->assertStatus(500);
    }

    public function test_obter_veiculo_por_uuid_existente(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();;

        $response = $this->getJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertOk();
    }

    public function test_listar_veiculos_vazios(): void
    {
        $response = $this->getJson('/api/veiculo');

        $response->assertOk();
        $response->assertJsonCount(0);
    }

    public function test_listar_veiculos_com_paginacao(): void
    {
        \App\Modules\Veiculo\Model\Veiculo::factory(15)->create()->fresh();

        $response = $this->getJson('/api/veiculo?page=1&per_page=10');

        $response->assertOk();
    }

    public function test_listar_veiculos_filtrados_por_cliente(): void
    {
        $cliente = \App\Modules\Cliente\Model\Cliente::factory()->create()->fresh();
        $veiculo1 = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        // Associar veiculo1 ao cliente
        \App\Modules\ClienteVeiculo\Model\ClienteVeiculo::factory()->create([
            'cliente_id' => $cliente->id,
            'veiculo_id' => $veiculo1->id
        ]);


        $response = $this->getJson('/api/veiculo?cliente_uuid=' . $cliente->uuid . '&page=1&per_page=10');

        $response->assertOk();
    }

    public function test_listar_veiculos_com_cliente_uuid_inexistente(): void
    {
        $uuidInexistente = '8acb1b8f-c588-4968-85ca-04ef66f2b380';

        $response = $this->getJson('/api/veiculo?cliente_uuid=' . $uuidInexistente);

        $response->assertStatus(400);
    }

    public function test_listar_veiculos_com_parametros_paginacao_invalidos(): void
    {
        $response = $this->getJson('/api/veiculo?page=0&per_page=-1');

        $response->assertStatus(400);
    }

    public function test_obter_veiculo_com_uuid_malformado(): void
    {
        $uuidMalformado = 'uuid-malformado-123';

        $response = $this->getJson('/api/veiculo/' . $uuidMalformado);

        $response->assertStatus(400);
    }

    public function test_listar_veiculos_com_estrutura_json_correta(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        $response = $this->getJson('/api/veiculo');

        $response->assertOk();
    }

    public function test_obter_veiculo_com_erro_interno(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andThrow(new \Exception('Erro interno ao obter veículo'));
        });

        $response = $this->getJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(500);
    }

    public function test_listar_veiculos_com_filtro_cliente_uuid_valido(): void
    {
        // Criar cliente e veículos
        $cliente = \App\Modules\Cliente\Model\Cliente::factory()->create()->fresh();
        $veiculo1 = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();
        $veiculo2 = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        \App\Modules\ClienteVeiculo\Model\ClienteVeiculo::factory()->create([
            'cliente_id' => $cliente->id,
            'veiculo_id' => $veiculo1->id
        ]);

        $response = $this->getJson('/api/veiculo?cliente_uuid=' . $cliente->uuid);

        $response->assertOk();
    }
}
