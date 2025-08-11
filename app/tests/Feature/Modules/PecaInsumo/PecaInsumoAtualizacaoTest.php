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
            'gtin' => $fake->ean13(),
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

        $response = $this->withAuth()->putJson('/api/peca-insumo/' . $pecaInsumo->uuid, $dadosAtualizacao);

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
        });
    }

    public function test_atualizar_servico_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $this->payload['descricao'] = 'Peca/Insumo com UUID que não existe';
        $this->payload['valor_custo'] = '200.00';
        $this->payload['status'] = 'ativo';
        $response = $this->withAuth()->putJson('/api/peca-insumo/' . $uuid, $this->payload);

        $response->assertStatus(400);
    }

    public function test_atualizar_peca_insumo_com_dados_invalidos(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->createOne()->fresh();

        $dadosInvalidos = [
            'gtin' => '',
            'descricao' => '',
            'valor_custo' => 'invalid',
            'valor_venda' => -10,
            'qtd_atual' => 'abc',
            'qtd_segregada' => -5,
            'status' => 'status_invalido'
        ];

        $response = $this->withAuth()->putJson('/api/peca-insumo/' . $pecaInsumo->uuid, $dadosInvalidos);

        $response->assertStatus(400);
    }

    public function test_atualizar_peca_insumo_com_uuid_mal_formado(): void
    {
        $uuidInvalido = 'uuid-mal-formado-123';

        $response = $this->withAuth()->putJson('/api/peca-insumo/' . $uuidInvalido, $this->payload);

        $response->assertStatus(400);
    }

    public function test_atualizar_peca_insumo_sem_payload(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->createOne()->fresh();

        $response = $this->withAuth()->putJson('/api/peca-insumo/' . $pecaInsumo->uuid, []);

        $response->assertStatus(400);
    }

    public function test_atualizar_peca_insumo_com_erro_interno(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->createOne()->fresh();

        $this->mock(\App\Modules\PecaInsumo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('atualizacao')
                 ->once()
                 ->andThrow(new \Exception('Erro interno simulado'));
        });

        $response = $this->withAuth()->putJson('/api/peca-insumo/' . $pecaInsumo->uuid, $this->payload);

        $response->assertStatus(500);
    }

    public function test_atualizar_peca_insumo_com_database_exception(): void
    {
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->createOne()->fresh();

        // Mock do service para simular erro de database
        $this->mock(\App\Modules\PecaInsumo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('atualizacao')
                 ->once()
                 ->andThrow(new \Illuminate\Database\QueryException(
                     'connection',
                     'SELECT * FROM users',
                     [],
                     new \Exception('Database connection failed')
                 ));
        });

        $response = $this->withAuth()->putJson('/api/peca-insumo/' . $pecaInsumo->uuid, $this->payload);

        $response->assertStatus(500);
        $response->assertJson([
            'error' => true
        ]);
        $response->assertJsonStructure([
            'error',
            'message'
        ]);
    }
}
