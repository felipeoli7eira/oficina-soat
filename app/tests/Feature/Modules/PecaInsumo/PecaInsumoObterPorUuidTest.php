<?php

namespace Tests\Feature\Modules\PecaInsumo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class PecaInsumoObterPorUuidTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('peca_insumo');
    }

    public function test_obter_peca_insumo_por_uuid(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory(1)->createOne()->fresh();
        $response = $this->getJson('/api/peca-insumo/' . $pecaInsumo->uuid);
        $response->assertOk();
    }

    public function test_obter_peca_insumo_com_encoding_error(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->createOne()->fresh();

        // Mock do service para retornar dados com caracteres não UTF-8
        $this->mock(\App\Modules\PecaInsumo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andReturn([
                    'descricao' => "\x80\x81\x82\x83" // Caracteres inválidos UTF-8
                ]);
        });

        $response = $this->getJson('/api/peca-insumo/' . $pecaInsumo->uuid);

        $response->assertStatus(500);
    }
}
