<?php

namespace Tests\Feature\Modules\Veiculo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VeiculoRemocaoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('veiculo');
    }

    public function test_remocao_do_veiculo_por_uuid(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();
        $response = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);
        $response->assertStatus(204);
    }

    public function test_remocao_do_veiculo_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->deleteJson('/api/veiculo/' . $uuid);
        $response->assertStatus(400);
    }

    public function test_remocao_veiculo_com_erro_interno(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('remocao')
                ->once()
                ->andThrow(new \Exception('Erro interno simulado na remocao'));
        });

        $response = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(500);
    }

    public function test_remocao_veiculo_com_uuid_malformado(): void
    {
        $uuidMalformado = 'uuid-malformado-123';

        $response = $this->deleteJson('/api/veiculo/' . $uuidMalformado);

        $response->assertStatus(400);
        $response->assertJsonStructure(['message', 'errors']);
    }

    public function test_remocao_veiculo_com_relacionamentos(): void
    {
        $cliente = \App\Modules\Cliente\Model\Cliente::factory()->create()->fresh();
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        // Criar relacionamento veículo-cliente
        $clienteVeiculo = \App\Modules\ClienteVeiculo\Model\ClienteVeiculo::factory()->create([
            'cliente_id' => $cliente->id,
            'veiculo_id' => $veiculo->id
        ]);

        $response = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(204);
    }

    public function test_remocao_veiculo_com_database_exception(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('route')->andReturn('veiculo.remover');
            $mock->shouldReceive('remocao')
                ->once()
                ->andThrow(new \Illuminate\Database\QueryException(
                    'connection',
                    'DELETE FROM veiculo WHERE uuid = ?',
                    [],
                    new \Exception('Database connection failed')
                ));
        });

        $response = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(500);
    }

    public function test_remocao_veiculo_com_constraint_violation(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->create()->fresh();

        // Mock para simular violação de constraint (ex: foreign key)
        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('remocao')
                ->once()
                ->andThrow(new \Illuminate\Database\QueryException(
                    'connection',
                    'DELETE FROM veiculo WHERE uuid = ?',
                    [$mock],
                    new \Exception('FOREIGN KEY constraint failed')
                ));
        });

        $response = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(500);
    }
}
