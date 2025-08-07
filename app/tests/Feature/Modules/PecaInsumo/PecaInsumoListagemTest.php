<?php

namespace Tests\Feature\Modules\PecaInsumo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PecaInsumoListagemTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('peca_insumo');
    }

    public function test_listar_todos_peca_insumos(): void
    {
        $pecaInsumos = \App\Modules\PecaInsumo\Model\PecaInsumo::factory(3)->create();
        $response = $this->getJson('/api/peca-insumo');

        $response->assertOk();
    }

    public function test_listar_peca_insumos_com_erro_interno(): void
    {
        $this->mock(\App\Modules\PecaInsumo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('listagem')
                ->once()
                ->andThrow(new \Exception('Erro interno na listagem'));
        });

        $response = $this->getJson('/api/peca-insumo');

        $response->assertStatus(500);
    }
}
