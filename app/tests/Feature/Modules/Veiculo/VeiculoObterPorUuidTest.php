<?php

namespace Tests\Feature\Modules\Veiculo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class VeiculoObterPorUuidTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('veiculo');
    }

    public function test_obter_veiculo_por_uuid(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        $response = $this->withAuth()->getJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertOk();
    }

    public function test_obter_veiculo_com_encoding_error(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        // Mock do service para retornar dados com caracteres não UTF-8
        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andReturn([
                    'marca' => "\x80\x81\x82\x83" // Caracteres inválidos UTF-8
                ]);
        });

        $response = $this->withAuth()->getJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(500);
    }

    public function test_obter_veiculo_por_uuid_inexistente(): void
    {
        $uuidInexistente = '8acb1b8f-c588-4968-85ca-04ef66f2b380';

        $response = $this->withAuth()->getJson('/api/veiculo/' . $uuidInexistente);

        $response->assertStatus(400);
    }

    public function test_obter_veiculo_com_uuid_malformado(): void
    {
        $uuidMalformado = 'uuid-malformado-123';

        $response = $this->withAuth()->getJson('/api/veiculo/' . $uuidMalformado);

        $response->assertStatus(400);
    }

    public function test_obter_veiculo_com_uuid_vazio(): void
    {
        $response = $this->withAuth()->getJson('/api/veiculo/');

        // Deve retornar 404 ou redirecionamento para listagem
        $this->assertTrue(
            $response->status() === 404 ||
            $response->status() === 301 ||
            $response->status() === 200
        );
    }

    public function test_obter_veiculo_com_erro_interno(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andThrow(new \Exception('Erro interno ao buscar veículo'));
        });

        $response = $this->withAuth()->getJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(500);
    }

    public function test_obter_veiculo_com_database_exception(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andThrow(new \Illuminate\Database\QueryException(
                    'connection',
                    'SELECT * FROM veiculo WHERE uuid = ?',
                    [$mock],
                    new \Exception('Database connection failed')
                ));
        });

        $response = $this->withAuth()->getJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(500);
    }

    public function test_obter_veiculo_estrutura_response_completa(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        $response = $this->withAuth()->getJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertOk();
        $response->assertJsonStructure([
            'uuid',
            'marca',
            'modelo',
            'placa',
            'ano_fabricacao',
            'cor',
            'chassi',
            'excluido',
            'data_cadastro',
            'data_atualizacao'
        ]);
    }

    public function test_obter_veiculo_com_relacionamentos(): void
    {
        $cliente = \App\Modules\Cliente\Model\Cliente::factory()->create()->fresh();
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        // Criar relacionamento veículo-cliente
        \App\Modules\ClienteVeiculo\Model\ClienteVeiculo::factory()->create([
            'cliente_id' => $cliente->id,
            'veiculo_id' => $veiculo->id
        ]);

        $response = $this->withAuth()->getJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertOk();
    }
}
