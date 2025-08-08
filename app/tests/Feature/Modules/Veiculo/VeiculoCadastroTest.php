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

        $marcas = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Volkswagen', 'Fiat', 'Hyundai', 'Nissan'];
        $modelos = ['Corolla', 'Civic', 'Focus', 'Onix', 'Gol', 'Uno', 'HB20', 'March'];
        $cores = ['Branco', 'Preto', 'Prata', 'Vermelho', 'Azul', 'Cinza', 'Bege'];

        $this->payload = [
            'marca' => $fake->randomElement($marcas),
            'modelo' => $fake->randomElement($modelos),
            'ano' => $fake->numberBetween(1990, date('Y') + 1),
            'placa' => strtoupper($fake->lexify('???-????')),
            'cor' => $fake->randomElement($cores),
            'chassi' => strtoupper($fake->lexify('?????????????????'))
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
        $fake = fake('pt_BR');

        $novo = $this->payload;
        $novo['placa'] = $fake->lexify('PLACA_INVALIDA_???');
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
        $fake = fake('pt_BR');

        $this->payload['modelo'] = $fake->lexify('?'); // Muito curto

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
    }

    public function test_chassi_eh_obrigatorio_e_deve_ter_17_caracteres(): void
    {
        $fake = fake('pt_BR');

        $this->payload['chassi'] = $fake->lexify('?????????');

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
        $fake = fake('pt_BR');

        $placa = strtoupper($fake->lexify('???-????'));
        \App\Modules\Veiculo\Model\Veiculo::factory()->createOne(['placa' => $placa]);

        $this->payload['placa'] =  $placa;

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
    }

    public function test_cadastrar_veiculo_com_chassi_duplicado(): void
    {
        $fake = fake('pt_BR');

        // Criar um veículo primeiro
        $chassi = strtoupper($fake->lexify('?????????????????'));
        \App\Modules\Veiculo\Model\Veiculo::factory()->createOne(['chassi' => $chassi]);

        $this->payload['chassi'] = $chassi;

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
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
        $fake = fake('pt_BR');

        $this->payload['cliente_uuid'] = $fake->uuid();

        $response = $this->postJson('/api/veiculo', $this->payload);
        $response->assertStatus(400);
    }

    public function test_cadastrar_veiculo_com_placa_formato_mercosul(): void
    {
        $fake = fake('pt_BR');

        $this->payload['placa'] = strtoupper($fake->lexify('???#?##'));

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
