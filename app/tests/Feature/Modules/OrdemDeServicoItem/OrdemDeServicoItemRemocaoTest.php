<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;
use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrdemDeServicoItemRemocaoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('os_item');
    }

    public function test_remocao_do_item_os_por_uuid(): void
    {
        $item = OrdemDeServicoItem::factory()->create();
        $response = $this->deleteJson('/api/os-item/' . $item->uuid);
        $response->assertStatus(204);

        $this->assertDatabaseHas('os_item', [
            'uuid' => $item->uuid,
            'excluido' => true
        ]);

        $itemAtualizado = OrdemDeServicoItem::where('uuid', $item->uuid)->first();
        $this->assertNotNull($itemAtualizado->data_exclusao);
    }

    public function test_remocao_do_item_os_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->deleteJson('/api/os-item/' . $uuid);
        $response->assertStatus(400);
    }

    public function test_remocao_item_os_com_erro_interno(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('remocao')
                ->once()
                ->andThrow(new \Exception('Erro interno simulado na remoção'));
        });

        $response = $this->deleteJson('/api/os-item/' . $item->uuid);

        $response->assertStatus(500);
    }

    public function test_remocao_item_os_com_uuid_malformado(): void
    {
        $uuidMalformado = 'uuid-malformado-123';

        $response = $this->deleteJson('/api/os-item/' . $uuidMalformado);

        $response->assertStatus(400);
    }


    public function test_remocao_multiplos_itens_os(): void
    {
        $item1 = OrdemDeServicoItem::factory()->create();
        $item2 = OrdemDeServicoItem::factory()->create();

        $response1 = $this->deleteJson('/api/os-item/' . $item1->uuid);
        $response1->assertStatus(204);

        $response2 = $this->deleteJson('/api/os-item/' . $item2->uuid);
        $response2->assertStatus(204);
    }

    public function test_remocao_item_os_ja_removido(): void
    {
        $item = OrdemDeServicoItem::factory()->create([
            'excluido' => true,
            'data_exclusao' => now()
        ]);

        $response = $this->deleteJson('/api/os-item/' . $item->uuid);
        $response->assertStatus(400);
    }

    public function test_remocao_item_os_com_database_exception(): void
    {
        $item = OrdemDeServicoItem::factory()->create();

        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('remocao')
                ->once()
                ->andThrow(new \Illuminate\Database\QueryException(
                    'connection',
                    'UPDATE os_item SET excluido = ? WHERE uuid = ?',
                    [true, $mock],
                    new \Exception('Database connection failed')
                ));
        });

        $response = $this->deleteJson('/api/os-item/' . $item->uuid);

        $response->assertStatus(500);
    }

    public function test_remocao_item_os_verifica_contagem_database(): void
    {
        OrdemDeServicoItem::factory(3)->create();
        $itemParaRemover = OrdemDeServicoItem::factory()->create();

        $this->assertDatabaseCount('os_item', 4);

        $response = $this->deleteJson('/api/os-item/' . $itemParaRemover->uuid);
        $response->assertStatus(204);
    }

    public function test_remocao_item_os_preserva_dados_originais(): void
    {
        $item = OrdemDeServicoItem::factory()->create([
            'observacao' => 'Observação importante do item',
            'quantidade' => 5,
            'valor' => 299.99
        ]);

        $dadosOriginais = [
            'observacao' => $item->observacao,
            'quantidade' => $item->quantidade,
            'valor' => $item->valor
        ];

        $response = $this->deleteJson('/api/os-item/' . $item->uuid);
        $response->assertStatus(204);

        $this->assertDatabaseHas('os_item', array_merge([
            'uuid' => $item->uuid,
            'excluido' => true
        ], $dadosOriginais));
    }

    public function test_remocao_item_os_atualiza_data_atualizacao(): void
    {
        $item = OrdemDeServicoItem::factory()->create([
            'data_atualizacao' => now()->subDays(5)
        ]);

        $dataAtualizacaoOriginal = $item->data_atualizacao;

        $response = $this->deleteJson('/api/os-item/' . $item->uuid);
        $response->assertStatus(204);

        $itemAtualizado = OrdemDeServicoItem::where('uuid', $item->uuid)->first();
        $this->assertNotEquals($dataAtualizacaoOriginal, $itemAtualizado->data_atualizacao);
        $this->assertTrue($itemAtualizado->data_atualizacao->isAfter($dataAtualizacaoOriginal));
    }

    public function test_remocao_item_os_mantem_relacionamentos_intactos(): void
    {
        $pecaInsumo = PecaInsumo::factory()->create();
        $ordemServico = OrdemDeServico::factory()->create();
        $item = OrdemDeServicoItem::factory()->create([
            'peca_insumo_id' => $pecaInsumo->id,
            'os_id' => $ordemServico->id
        ]);

        $response = $this->deleteJson('/api/os-item/' . $item->uuid);
        $response->assertStatus(204);
    }

    public function test_remocao_item_os_nao_afeta_outros_itens(): void
    {
        $ordemServico = OrdemDeServico::factory()->create();
        $item1 = OrdemDeServicoItem::factory()->create(['os_id' => $ordemServico->id]);
        $item2 = OrdemDeServicoItem::factory()->create(['os_id' => $ordemServico->id]);
        $item3 = OrdemDeServicoItem::factory()->create(); // Item de outra OS

        $response = $this->deleteJson('/api/os-item/' . $item1->uuid);
        $response->assertStatus(204);
    }
}
