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

    public function test_exclusao_logica_do_peca_insumo_por_id(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory(1)->createOne()->fresh();
        $response = $this->deleteJson('/api/peca_insumo/' . $pecaInsumo->id);
        $response->assertNoContent();

        $response = $this->getJson('/api/peca_insumo/' . $pecaInsumo->id);
        $response->assertNotFound();
    }

    public function test_exclusao_logica_do_peca_insumo_por_id_que_nao_existe(): void
    {
        $id = fake()->numberBetween(100000, 999999);
        $response = $this->deleteJson('/api/peca_insumo/' . $id);
        $response->assertNotFound();
    }
}
