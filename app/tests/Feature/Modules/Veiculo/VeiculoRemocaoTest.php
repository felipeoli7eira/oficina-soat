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
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory(1)->createOne()->fresh();
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
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

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
        $cliente = \App\Modules\Cliente\Model\Cliente::factory()->createOne();
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        // Criar relacionamento veículo-cliente
        $clienteVeiculo = \App\Modules\ClienteVeiculo\Model\ClienteVeiculo::factory()->createOne([
            'cliente_id' => $cliente->id,
            'veiculo_id' => $veiculo->id
        ]);

        $response = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(204);
    }

    public function test_remocao_veiculo_multiplos(): void
    {
        $veiculo1 = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();
        $veiculo2 = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        // Remover primeiro veículo
        $response1 = $this->deleteJson('/api/veiculo/' . $veiculo1->uuid);
        $response1->assertStatus(204);

        // Remover segundo veículo
        $response2 = $this->deleteJson('/api/veiculo/' . $veiculo2->uuid);
        $response2->assertStatus(204);
    }

    public function test_remocao_veiculo_ja_removido(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        // Primeira remoção
        $response1 = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);
        $response1->assertStatus(204);

        // Tentar remover novamente o mesmo veículo
        $response2 = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);
        $response2->assertStatus(400); // UUID não existe mais
    }

    public function test_remocao_veiculo_com_database_exception(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('remocao')
                ->once()
                ->andThrow(new \Illuminate\Database\QueryException(
                    'connection',
                    'DELETE FROM veiculo WHERE uuid = ?',
                    [$mock],
                    new \Exception('Database connection failed')
                ));
        });

        $response = $this->deleteJson('/api/veiculo/' . $veiculo->uuid);

        $response->assertStatus(500);
    }

    public function test_remocao_veiculo_verifica_contagem_database(): void
    {
        // Criar múltiplos veículos
        \App\Modules\Veiculo\Model\Veiculo::factory(3)->create();
        $veiculoParaRemover = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        $this->assertDatabaseCount('veiculo', 4);

        $response = $this->deleteJson('/api/veiculo/' . $veiculoParaRemover->uuid);
        $response->assertStatus(204);

    }

    public function test_remocao_veiculo_com_constraint_violation(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

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
