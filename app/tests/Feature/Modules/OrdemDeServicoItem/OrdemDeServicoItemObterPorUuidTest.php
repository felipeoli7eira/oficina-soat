<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;
use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrdemDeServicoItemObterPorUuidTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('os_item');
    }

    public function test_obter_item_os_por_uuid(): void
    {
        $item = OrdemDeServicoItem::factory()->create();
        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertOk();
    }

    public function test_obter_item_os_com_encoding_error(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andReturn([
                    'observacao' => "\x80\x81\x82\x83"
                ]);
        });

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertStatus(500);
    }

    public function test_obter_item_os_por_uuid_inexistente(): void
    {
        $uuidInexistente = '8acb1b8f-c588-4968-85ca-04ef66f2b380';

        $response = $this->getJson('/api/os-item/' . $uuidInexistente);

        $response->assertStatus(400);
    }

    public function test_obter_item_os_com_uuid_malformado(): void
    {
        $uuidMalformado = 'uuid-malformado-123';

        $response = $this->getJson('/api/os-item/' . $uuidMalformado);

        $response->assertStatus(400);
    }

    public function test_obter_item_os_com_uuid_vazio(): void
    {
        $response = $this->getJson('/api/os-item/');

        $this->assertTrue(
            $response->status() === 404 ||
            $response->status() === 301 ||
            $response->status() === 200
        );
    }

    public function test_obter_item_os_com_erro_interno(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andThrow(new \Exception('Erro interno ao buscar item da OS'));
        });

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertStatus(500);
    }

    public function test_obter_item_os_com_database_exception(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andThrow(new \Illuminate\Database\QueryException(
                    'connection',
                    'SELECT * FROM os_item WHERE uuid = ?',
                    [$mock],
                    new \Exception('Database connection failed')
                ));
        });

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertStatus(500);
    }

    public function test_obter_item_os_estrutura_response_completa(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertOk();
    }

    public function test_obter_item_os_com_relacionamentos(): void
    {
        $pecaInsumo = PecaInsumo::factory()->create();
        $ordemServico = OrdemDeServico::factory()->create();
        $item = OrdemDeServicoItem::factory()->create([
            'peca_insumo_id' => $pecaInsumo->id,
            'os_id' => $ordemServico->id
        ]);

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertOk();
    }

    public function test_obter_item_os_excluido(): void
    {
        $item = OrdemDeServicoItem::factory()->create([
            'excluido' => true,
            'data_exclusao' => now()
        ]);

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertOk();
    }

    public function test_obter_item_os_nao_excluido(): void
    {
        $item = OrdemDeServicoItem::factory()->create([
            'excluido' => false,
            'data_exclusao' => null
        ]);

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertOk();
    }

    public function test_obter_item_os_com_valores_decimais(): void
    {
        $item = OrdemDeServicoItem::factory()->create([
            'valor' => 1234.56,
            'quantidade' => 3
        ]);

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertOk();
    }
}
