<?php

namespace Tests\Feature\Modules\Cliente;

use App\Modules\Cliente\Model\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteRemocaoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('cliente');
    }

    public function test_remocao_logica_do_cliente_por_uuid(): void
    {
        $cliente = Cliente::factory()->createOne()->fresh();
        $response = $this->withAuth()->deleteJson('/api/cliente/' . $cliente->uuid);
        $response->assertStatus(204);

        $response = $this->withAuth()->getJson('/api/cliente/' . $cliente->uuid);
        $response->assertStatus(400);
    }

    public function test_remocao_logica_do_cliente_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->withAuth()->deleteJson('/api/cliente/' . $uuid);
        $response->assertStatus(400);
    }

    public function test_remocao_cliente_com_erro_interno(): void
    {
        $cliente = Cliente::factory()->createOne()->fresh();

        $this->mock(\App\Modules\Cliente\Service\Service::class, function ($mock) {
            $mock->shouldReceive('remocao')
                ->once()
                ->andThrow(new \Exception('Erro interno simulado na remocao'));
        });

        $response = $this->withAuth()->deleteJson('/api/cliente/' . $cliente->uuid);

        $response->assertStatus(500);
    }
}
