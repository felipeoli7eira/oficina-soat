<?php

namespace Tests\Feature\Modules\Veiculo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VeiculoAtualizacaoTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('veiculo');

        $fake = fake('pt_BR');

        $this->payload = [
            'placa' => 'ABC-1234',
            'cor' => 'Branco'
        ];
    }

    public function test_atualizar_veiculo_por_uuid(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        $dadosAtualizacao = [
            'placa' => $veiculo->placa,
            'cor' => 'Azul'
        ];

        $response = $this->putJson('/api/veiculo/' . $veiculo->uuid, $dadosAtualizacao);

        $response->assertOk();
    }

    public function test_atualizar_veiculo_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $this->payload['cor'] = 'Veículo com UUID que não existe';

        $response = $this->putJson('/api/veiculo/' . $uuid, $this->payload);

        $response->assertStatus(400);
    }

    public function test_atualizar_veiculo_com_dados_invalidos(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        $dadosInvalidos = [
            'placa' => 'PLACA_INVALIDA_123',
            'cor' => '',
            'cliente_uuid' => 'uuid_invalido'
        ];

        $response = $this->putJson('/api/veiculo/' . $veiculo->uuid, $dadosInvalidos);

        $response->assertStatus(400);
    }

    public function test_atualizar_veiculo_com_uuid_mal_formado(): void
    {
        $uuidInvalido = 'uuid-mal-formado-123';

        $response = $this->putJson('/api/veiculo/' . $uuidInvalido, $this->payload);

        $response->assertStatus(400);
    }

    public function test_atualizar_veiculo_sem_payload(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        $response = $this->putJson('/api/veiculo/' . $veiculo->uuid, []);

        $response->assertStatus(400);
    }

    public function test_atualizar_veiculo_com_erro_interno(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('atualizacao')
                 ->once()
                 ->andThrow(new \Exception('Erro interno simulado'));
        });

        $response = $this->putJson('/api/veiculo/' . $veiculo->uuid, $this->payload);

        $response->assertStatus(500);
    }

    public function test_atualizar_veiculo_com_database_exception(): void
    {
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        // Mock do service para simular erro de database
        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('atualizacao')
                 ->once()
                 ->andThrow(new \Illuminate\Database\QueryException(
                     'connection',
                     'SELECT * FROM veiculo',
                     [],
                     new \Exception('Database connection failed')
                 ));
        });

        $response = $this->putJson('/api/veiculo/' . $veiculo->uuid, $this->payload);

        $response->assertStatus(500);
        $response->assertJson([
            'error' => true
        ]);
        $response->assertJsonStructure([
            'error',
            'message'
        ]);
    }

    public function test_atualizar_veiculo_com_cliente_uuid(): void
    {
        $cliente = \App\Modules\Cliente\Model\Cliente::factory()->createOne()->fresh();
        $veiculo = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne()->fresh();

        $dadosAtualizacao = [
            'placa' => $veiculo->placa,
            'cor' => 'Verde',
            'cliente_uuid' => $cliente->uuid
        ];

        $response = $this->putJson('/api/veiculo/' . $veiculo->uuid, $dadosAtualizacao);

        $response->assertOk();
    }

    public function test_atualizar_veiculo_com_placa_duplicada(): void
    {
        $veiculo1 = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne(['placa' => 'ABC-1234'])->fresh();
        $veiculo2 = \App\Modules\Veiculo\Model\Veiculo::factory()->createOne(['placa' => 'XYZ-9876'])->fresh();

        $dadosAtualizacao = [
            'placa' => 'ABC-1234', // Placa já existente
            'cor' => $veiculo2->cor
        ];

        $response = $this->putJson('/api/veiculo/' . $veiculo2->uuid, $dadosAtualizacao);

        $response->assertStatus(400);
    }
}
