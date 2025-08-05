<?php

namespace Tests\Feature\Modules\PecaInsumo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PecaInsumoAtualizacaoTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('peca_insumo');

        $fake = fake('pt_BR');

        $this->payload = [
            'gtin' => '7891234567890',
            'descricao' => 'Filtro de Óleo Motor',
            'valor_custo' => 25.50,
            'valor_venda' => 45.90,
            'qtd_atual' => 100,
            'qtd_segregada' => 5,
            'status' => 'ativo'
        ];
    }

    public function test_atualizar_peca_insumo_por_uuid(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->createOne()->fresh();

        $dadosAtualizacao = [
            'gtin' => $pecaInsumo->gtin,
            'descricao' => 'Peça de Insumo Atualizada',
            'valor_custo' => '200.00',
            'valor_venda' => $pecaInsumo->valor_venda,
            'qtd_atual' => $pecaInsumo->qtd_atual,
            'qtd_segregada' => $pecaInsumo->qtd_segregada,
            'status' => 'ativo'
        ];

        $response = $this->putJson('/api/peca-insumo/' . $pecaInsumo->uuid, $dadosAtualizacao);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($dadosAtualizacao, $pecaInsumo) {
            $json->has('gtin')
                 ->has('descricao')
                 ->has('valor_custo')
                 ->has('valor_venda')
                 ->has('qtd_atual')
                 ->has('qtd_segregada')
                 ->has('status')
                 ->etc();

            $json->whereAll([
                'gtin' => $pecaInsumo->gtin,
                'descricao' => 'Peça de Insumo Atualizada',
                'valor_venda' => (string) $pecaInsumo->valor_venda,
                'qtd_atual' => (int) $pecaInsumo->qtd_atual,
                'qtd_segregada' => (int) $pecaInsumo->qtd_segregada,
                'valor_custo' => '200.00',
                'status' => 'ativo',
            ]);
        });
    }

    public function test_atualizar_servico_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $this->payload['descricao'] = 'Peca/Insumo com UUID que não existe';
        $this->payload['valor_custo'] = '200.00';
        $this->payload['status'] = 'ativo';
        $response = $this->putJson('/api/peca-insumo/' . $uuid, $this->payload);

        $response->assertNotFound();
    }
}
