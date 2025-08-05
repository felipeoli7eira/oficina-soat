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

    public function test_exclusao_logica_do_peca_insumo_por_uuid(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory(1)->createOne()->fresh();
        $response = $this->deleteJson('/api/peca-insumo/' . $pecaInsumo->uuid);
        $response->assertNoContent();

        $response = $this->getJson('/api/peca-insumo/' . $pecaInsumo->uuid);
        $response->assertNotFound();
    }

    public function test_exclusao_logica_do_peca_insumo_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->deleteJson('/api/peca-insumo/' . $uuid);
        $response->assertNotFound();
    }
}
