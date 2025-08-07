<?php

namespace Tests\Feature\Modules\Veiculo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VeiculoCadastroTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('veiculo');

        $fake = fake('pt_BR');

        $this->payload = [
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'ano' => 2020,
            'placa' => 'ABC-1234',
            'cor' => 'Branco',
            'chassi' => '9BWZZZ377VT004251'
        ];
    }

    public function test_cadastrar_veiculo(): void
    {
        $response = $this->postJson('/api/veiculo', $this->payload);

        $response->assertCreated();
        $this->assertDatabaseCount('veiculo', 1);
    }

    public function test_marca_eh_obrigatoria_e_deve_ter_minimo_2_caracteres(): void
    {
        $this->payload['marca'] = '';

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
        $response->assertJsonStructure(['error', 'message', 'errors']);
    }

    public function test_ano_do_veiculo_deve_ser_valido(): void
    {
        $novo = $this->payload;
        $novo['ano'] = 1800; // Ano anterior ao permitido
        $response = $this->postJson('/api/veiculo', $novo);

        $response->assertStatus(400);
    }

    public function test_placa_do_veiculo_deve_ter_formato_valido(): void
    {
        $novo = $this->payload;
        $novo['placa'] = 'PLACA_INVALIDA';
        $response = $this->postJson('/api/veiculo', $novo);

        $response->assertStatus(400);
    }

    public function test_cadastrar_veiculo_com_erro_interno(): void
    {
        $this->mock(\App\Modules\Veiculo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('cadastro')
                ->once()
                ->andThrow(new \Exception('Erro interno simulado no cadastro'));
        });

        $response = $this->postJson('/api/veiculo', $this->payload);

        $response->assertStatus(500);
    }

    public function test_modelo_eh_obrigatorio_e_deve_ter_minimo_2_caracteres(): void
    {
        $this->payload['modelo'] = 'A'; // Muito curto

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
    }

    public function test_chassi_eh_obrigatorio_e_deve_ter_17_caracteres(): void
    {
        $this->payload['chassi'] = '123456789'; // Muito curto

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
    }

    public function test_cor_eh_obrigatoria(): void
    {
        unset($this->payload['cor']);

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
    }

    public function test_cadastrar_veiculo_com_placa_duplicada(): void
    {
        // Criar um veículo primeiro
        \App\Modules\Veiculo\Model\Veiculo::factory()->createOne(['placa' => 'XYZ-9876']);

        $this->payload['placa'] = 'XYZ-9876'; // Placa duplicada

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
        $response->assertJsonPath('errors.placa.0', 'Esta placa já está cadastrada para outro veículo.');
    }

    public function test_cadastrar_veiculo_com_chassi_duplicado(): void
    {
        // Criar um veículo primeiro
        \App\Modules\Veiculo\Model\Veiculo::factory()->createOne(['chassi' => '1HGBH41JXMN109186']);

        $this->payload['chassi'] = '1HGBH41JXMN109186'; // Chassi duplicado

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
        $response->assertJsonPath('errors.chassi.0', 'Este chassi já está cadastrado para outro veículo.');
    }

    public function test_cadastrar_veiculo_com_cliente_uuid(): void
    {
        $cliente = \App\Modules\Cliente\Model\Cliente::factory()->createOne();
        $this->payload['cliente_uuid'] = $cliente->uuid;

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertCreated();
    }

    public function test_cadastrar_veiculo_com_cliente_uuid_inexistente(): void
    {
        $this->payload['cliente_uuid'] = '8acb1b8f-c588-4968-85ca-04ef66f2b380'; // UUID inexistente

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
        $response->assertJsonPath('errors.cliente_uuid.0', 'Cliente não encontrado.');
    }

    public function test_cadastrar_veiculo_com_placa_formato_mercosul(): void
    {
        $this->payload['placa'] = 'ABC1D23'; // Formato Mercosul

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertCreated();
    }

    public function test_ano_futuro_permitido(): void
    {
        $anoFuturo = date('Y') + 1;
        $this->payload['ano'] = $anoFuturo;

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertCreated();
    }

    public function test_ano_muito_futuro_nao_permitido(): void
    {
        $anoMuitoFuturo = date('Y') + 2;
        $this->payload['ano'] = $anoMuitoFuturo;

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
    }
}
