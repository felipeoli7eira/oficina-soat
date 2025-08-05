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

    public function test_listar_peca_insumo_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->getJson('/api/peca-insumo/' . $uuid);
        $response->assertNotFound();
    }
}
