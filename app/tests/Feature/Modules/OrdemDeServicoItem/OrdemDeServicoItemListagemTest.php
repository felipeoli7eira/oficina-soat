<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;
use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrdemDeServicoItemListagemTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('os_item');
    }

    public function test_listar_todos_itens_os(): void
    {
        $itens = OrdemDeServicoItem::factory(3)->create();
        $response = $this->getJson('/api/os-item');

        $response->assertOk();
        $response->assertJsonCount(3);
    }

    public function test_listar_item_os_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->getJson('/api/os-item/' . $uuid);
        $response->assertStatus(400);
    }

    public function test_listar_itens_os_com_erro_interno(): void
    {
        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('listagem')
                ->once()
                ->andThrow(new \Exception('Erro interno na listagem'));
        });

        $response = $this->getJson('/api/os-item');

        $response->assertStatus(500);
    }

    public function test_obter_item_os_por_uuid_existente(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertOk();
    }

    public function test_listar_itens_os_vazios(): void
    {
        $response = $this->getJson('/api/os-item');

        $response->assertOk();
        $response->assertJsonCount(0);
    }

    public function test_listar_itens_os_com_paginacao(): void
    {
        OrdemDeServicoItem::factory(15)->create();

        $response = $this->getJson('/api/os-item?page=1&per_page=10');

        $response->assertOk();
    }

    public function test_listar_itens_filtrados_por_os(): void
    {
        $ordemServico = OrdemDeServico::factory()->create();
        $item1 = OrdemDeServicoItem::factory()->create(['os_id' => $ordemServico->id]);
        $item2 = OrdemDeServicoItem::factory()->create(); // Item de outra OS

        $response = $this->getJson('/api/os-item?os_uuid=' . $ordemServico->uuid);

        $response->assertOk();
    }

    public function test_listar_itens_filtrados_por_peca_insumo(): void
    {
        $pecaInsumo = PecaInsumo::factory()->create();
        $item1 = OrdemDeServicoItem::factory()->create(['peca_insumo_id' => $pecaInsumo->id]);
        $item2 = OrdemDeServicoItem::factory()->create(); // Item de outra peça

        $response = $this->getJson('/api/os-item?peca_insumo_uuid=' . $pecaInsumo->uuid);

        $response->assertOk();
    }

    public function test_listar_itens_com_os_uuid_inexistente(): void
    {
        $uuidInexistente = '8acb1b8f-c588-4968-85ca-04ef66f2b380';

        $response = $this->getJson('/api/os-item?os_uuid=' . $uuidInexistente);

        $response->assertStatus(400);
    }

    public function test_listar_itens_com_peca_insumo_uuid_inexistente(): void
    {
        $uuidInexistente = '8acb1b8f-c588-4968-85ca-04ef66f2b380';

        $response = $this->getJson('/api/os-item?peca_insumo_uuid=' . $uuidInexistente);

        $response->assertStatus(400);
    }

    public function test_listar_itens_com_parametros_paginacao_invalidos(): void
    {
        $response = $this->getJson('/api/os-item?page=0&per_page=-1');

        $response->assertStatus(400);
    }

    public function test_obter_item_com_uuid_malformado(): void
    {
        $uuidMalformado = 'uuid-malformado-123';

        $response = $this->getJson('/api/os-item/' . $uuidMalformado);

        $response->assertStatus(400);
    }

    public function test_listar_itens_com_estrutura_json_correta(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $response = $this->getJson('/api/os-item');

        $response->assertOk();
    }

    public function test_obter_item_com_erro_interno(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('obterUmPorUuid')
                ->once()
                ->andThrow(new \Exception('Erro interno ao obter item'));
        });

        $response = $this->getJson('/api/os-item/' . $item->uuid);

        $response->assertStatus(500);
    }

    public function test_listar_itens_excluidos(): void
    {
        $itemNormal = OrdemDeServicoItem::factory()->create(['excluido' => false]);
        $itemExcluido = OrdemDeServicoItem::factory()->create([
            'excluido' => true,
            'data_exclusao' => now()
        ]);

        $response = $this->getJson('/api/os-item?incluir_excluidos=true');

        $response->assertOk();
    }

    public function test_listar_apenas_itens_nao_excluidos(): void
    {
        $itemNormal = OrdemDeServicoItem::factory()->create(['excluido' => false]);
        $itemExcluido = OrdemDeServicoItem::factory()->create([
            'excluido' => true,
            'data_exclusao' => now()
        ]);

        $response = $this->getJson('/api/os-item');

        $response->assertOk();
    }

    public function test_listar_itens_ordenados_por_data_cadastro(): void
    {
        $item1 = OrdemDeServicoItem::factory()->create(['data_cadastro' => now()->subDays(2)]);
        $item2 = OrdemDeServicoItem::factory()->create(['data_cadastro' => now()->subDay()]);
        $item3 = OrdemDeServicoItem::factory()->create(['data_cadastro' => now()]);

        $response = $this->getJson('/api/os-item?ordenar_por=data_cadastro&ordem=desc');

        $response->assertOk();
    }

    public function test_listar_itens_com_filtro_valor_minimo(): void
    {
        $itemBarato = OrdemDeServicoItem::factory()->create(['valor' => 50.00]);
        $itemCaro = OrdemDeServicoItem::factory()->create(['valor' => 500.00]);

        $response = $this->getJson('/api/os-item?valor_minimo=100');

        $response->assertOk();
    }

    public function test_listar_itens_com_filtro_valor_maximo(): void
    {
        $itemBarato = OrdemDeServicoItem::factory()->create(['valor' => 50.00]);
        $itemCaro = OrdemDeServicoItem::factory()->create(['valor' => 500.00]);

        $response = $this->getJson('/api/os-item?valor_maximo=100');

        $response->assertOk();
    }
}
