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
        $response = $this->getJson('/api/peca_insumo');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        $response->assertJson(function (AssertableJson $json) use($pecaInsumos){
            $json->has('data.0.id')
                 ->has('data.0.descricao')
                 ->has('data.0.valor_custo')
                 ->has('data.0.valor_venda')
                 ->has('data.0.qtd_atual')
                 ->has('data.0.qtd_segregada')
                 ->has('data.0.status')
                 ->etc();

            $pecaInsumos = $pecaInsumos->first();
            $json->whereAll([
                'data.0.descricao' => $pecaInsumos->descricao,
                'data.0.valor_custo' => (string) $pecaInsumos->valor_custo, // Converter para string pois vem do banco como string
                'data.0.valor_venda' => (string) $pecaInsumos->valor_venda, // Converter para string pois vem do banco como string
                'data.0.qtd_atual' => (int) $pecaInsumos->qtd_atual,
                'data.0.qtd_segregada' => (int) $pecaInsumos->qtd_segregada,
                'data.0.status' => $pecaInsumos->status,
            ]);
        });
    }

    public function test_listar_peca_insumo_por_id_que_nao_existe(): void
    {
        $id = fake()->numberBetween(100000, 999999);
        $response = $this->getJson('/api/peca_insumo/' . $id);
        $response->assertNotFound();
    }
}
