<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;
use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdemDeServicoItemCadastroTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;
    private PecaInsumo $pecaInsumo;
    private OrdemDeServico $ordemDeServico;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('os_item');

        $this->pecaInsumo = PecaInsumo::factory()->create();
        $this->ordemDeServico = OrdemDeServico::factory()->create();

        $this->payload = [
            'peca_insumo_uuid' => $this->pecaInsumo->uuid,
            'os_uuid' => $this->ordemDeServico->uuid,
            'observacao' => 'Item conforme solicitado pelo cliente',
            'quantidade' => 2,
            'valor' => 150.50
        ];
    }

    public function test_cadastrar_item_os(): void
    {
        $response = $this->postJson('/api/os-item', $this->payload);

        $response->assertCreated();
        $this->assertDatabaseCount('os_item', 1);
    }

    public function test_peca_insumo_uuid_eh_obrigatorio(): void
    {
        unset($this->payload['peca_insumo_uuid']);

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_os_uuid_eh_obrigatorio(): void
    {
        unset($this->payload['os_uuid']);

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_observacao_eh_obrigatoria_e_deve_ter_minimo_3_caracteres(): void
    {
        $this->payload['observacao'] = 'AB';
        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_quantidade_eh_obrigatoria_e_deve_ser_pelo_menos_1(): void
    {
        $this->payload['quantidade'] = 0;

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_valor_eh_obrigatorio_e_deve_ser_maior_que_zero(): void
    {
        $this->payload['valor'] = 0;

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_peca_insumo_uuid_deve_existir(): void
    {
        $this->payload['peca_insumo_uuid'] = '8acb1b8f-c588-4968-85ca-04ef66f2b380';

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_os_uuid_deve_existir(): void
    {
        $this->payload['os_uuid'] = '8acb1b8f-c588-4968-85ca-04ef66f2b380';

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_cadastrar_item_os_com_erro_interno(): void
    {
        $this->mock(\App\Modules\OrdemDeServicoItem\Service\Service::class, function ($mock) {
            $mock->shouldReceive('cadastro')
                ->once()
                ->andThrow(new \Exception('Erro interno simulado no cadastro'));
        });

        $response = $this->postJson('/api/os-item', $this->payload);

        $response->assertStatus(500);
    }

    public function test_peca_insumo_uuid_deve_ter_formato_valido(): void
    {
        $this->payload['peca_insumo_uuid'] = 'uuid-invalido';

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_os_uuid_deve_ter_formato_valido(): void
    {
        $this->payload['os_uuid'] = 'uuid-invalido';

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertStatus(400);
    }

    public function test_cadastrar_item_com_quantidade_alta(): void
    {
        $this->payload['quantidade'] = 100;

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertCreated();
    }

    public function test_cadastrar_item_com_valor_alto(): void
    {
        $this->payload['valor'] = 9999.99;

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertCreated();
    }

    public function test_cadastrar_item_com_observacao_maxima(): void
    {
        $this->payload['observacao'] = str_repeat('A', 500);

        $response = $this->postJson('/api/os-item', $this->payload);
        $response->assertCreated();
    }

    public function test_cadastrar_multiplos_itens_para_mesma_os(): void
    {
        $response1 = $this->postJson('/api/os-item', $this->payload);
        $response1->assertCreated();

        $pecaInsumo2 = PecaInsumo::factory()->create();
        $this->payload['peca_insumo_uuid'] = $pecaInsumo2->uuid;
        $this->payload['observacao'] = 'Segundo item da mesma OS';

        $response2 = $this->postJson('/api/os-item', $this->payload);
        $response2->assertCreated();

        $this->assertDatabaseCount('os_item', 2);
    }

    public function test_cadastrar_mesmo_item_para_oss_diferentes(): void
    {
        $response1 = $this->postJson('/api/os-item', $this->payload);
        $response1->assertCreated();

        $ordemDeServico2 = OrdemDeServico::factory()->create();
        $this->payload['os_uuid'] = $ordemDeServico2->uuid;
        $this->payload['observacao'] = 'Mesmo item para OS diferente';

        $response2 = $this->postJson('/api/os-item', $this->payload);
        $response2->assertCreated();
    }
}
