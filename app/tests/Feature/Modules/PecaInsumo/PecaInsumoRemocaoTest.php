<?php

namespace Tests\Feature\Modules\PecaInsumo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PecaInsumoRemocaoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('peca_insumo');
    }

    public function test_remocao_logica_do_peca_insumo_por_uuid(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory(1)->createOne()->fresh();
        $response = $this->withAuth()->deleteJson('/api/peca-insumo/' . $pecaInsumo->uuid);
        $response->assertStatus(204);

        $response = $this->getJson('/api/peca-insumo/' . $pecaInsumo->uuid);
        $response->assertStatus(400);
    }

    public function test_remocao_logica_do_peca_insumo_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->withAuth()->deleteJson('/api/peca-insumo/' . $uuid);
        $response->assertStatus(400);
    }

    public function test_remocao_peca_insumo_com_erro_interno(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->createOne()->fresh();

        $this->mock(\App\Modules\PecaInsumo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('remocao')
                ->once()
                ->andThrow(new \Exception('Erro interno simulado na remocao'));
        });

        $response = $this->withAuth()->deleteJson('/api/peca-insumo/' . $pecaInsumo->uuid);

        $response->assertStatus(500);
    }
}
