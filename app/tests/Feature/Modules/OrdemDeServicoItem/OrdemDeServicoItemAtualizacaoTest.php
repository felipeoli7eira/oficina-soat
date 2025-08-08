<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;
use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrdemDeServicoItemAtualizacaoTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;
    private OrdemDeServicoItem $osItem;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('os_item');
        $this->osItem = OrdemDeServicoItem::factory()->create();

        $this->payload = [
            'observacao' => 'Observação atualizada',
            'quantidade' => 3,
            'valor' => 299.99
        ];
    }

    public function test_atualizar_item_os_por_uuid(): void
    {
        $dadosAtualizacao = [
            'observacao' => 'Nova observação do item',
            'quantidade' => 5,
            'valor' => 450.75
        ];

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertOk();
        $this->assertDatabaseHas('os_item', [
            'uuid' => $this->osItem->uuid,
            'observacao' => 'Nova observação do item',
            'quantidade' => 5,
            'valor' => 450.75
        ]);
    }

    public function test_atualizar_item_os_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';

        $response = $this->putJson('/api/os-item/' . $uuid, $this->payload);

        $response->assertStatus(400);
    }

    public function test_atualizar_item_os_com_dados_invalidos(): void
    {
        $dadosInvalidos = [
            'observacao' => 'AB',
            'quantidade' => 0,
            'valor' => -10.50
        ];

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosInvalidos);

        $response->assertStatus(400);
    }

    public function test_atualizar_item_os_com_uuid_mal_formado(): void
    {
        $uuidInvalido = 'uuid-mal-formado-123';

        $response = $this->putJson('/api/os-item/' . $uuidInvalido, $this->payload);

        $response->assertStatus(400);
    }

    public function test_atualizar_item_os_sem_payload(): void
    {
        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, []);

        $response->assertStatus(400);
    }

    public function test_atualizar_item_os_com_erro_interno(): void
    {
        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('atualizacao')
                 ->once()
                 ->andThrow(new \Exception('Erro interno simulado'));
        });

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $this->payload);

        $response->assertStatus(500);
    }

    public function test_atualizar_item_os_com_database_exception(): void
    {
        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('atualizacao')
                 ->once()
                 ->andThrow(new \Illuminate\Database\QueryException(
                     'connection',
                     'UPDATE os_item SET observacao = ?',
                     ['Nova observação'],
                     new \Exception('Database connection failed')
                 ));
        });

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $this->payload);

        $response->assertStatus(500);
    }

    public function test_atualizar_apenas_observacao(): void
    {
        $dadosAtualizacao = [
            'observacao' => 'Somente observação alterada'
        ];

        $quantidadeOriginal = $this->osItem->quantidade;
        $valorOriginal = $this->osItem->valor;

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertOk();
    }

    public function test_atualizar_apenas_quantidade(): void
    {
        $dadosAtualizacao = [
            'quantidade' => 10
        ];

        $observacaoOriginal = $this->osItem->observacao;
        $valorOriginal = $this->osItem->valor;

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertOk();
    }

    public function test_atualizar_apenas_valor(): void
    {
        $dadosAtualizacao = [
            'valor' => 999.99
        ];

        $observacaoOriginal = $this->osItem->observacao;
        $quantidadeOriginal = $this->osItem->quantidade;

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertOk();
    }

    public function test_atualizar_observacao_com_tamanho_maximo(): void
    {
        $dadosAtualizacao = [
            'observacao' => str_repeat('A', 500) // Tamanho máximo permitido
        ];

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertOk();
    }

    public function test_atualizar_observacao_com_tamanho_excedente(): void
    {
        $dadosAtualizacao = [
            'observacao' => str_repeat('A', 501) // Excede o tamanho máximo
        ];

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertStatus(400);
        $response->assertJsonPath('errors.observacao.0', 'A observação não pode ter mais de 500 caracteres.');
    }

    public function test_atualizar_observacao_muito_curta(): void
    {
        $dadosAtualizacao = [
            'observacao' => 'AB' // Muito curta
        ];

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertStatus(400);
        $response->assertJsonPath('errors.observacao.0', 'A observação deve ter pelo menos 3 caracteres.');
    }

    public function test_atualizar_quantidade_zero(): void
    {
        $dadosAtualizacao = [
            'quantidade' => 0
        ];

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertStatus(400);
    }

    public function test_atualizar_valor_zero(): void
    {
        $dadosAtualizacao = [
            'valor' => 0
        ];

        $response = $this->putJson('/api/os-item/' . $this->osItem->uuid, $dadosAtualizacao);

        $response->assertStatus(400);
    }
}
