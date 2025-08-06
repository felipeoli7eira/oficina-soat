<?php

namespace Tests\Feature\Modules\Cliente;

use App\Enums\Papel;
use App\Modules\Cliente\Model\Cliente;
use App\Modules\Usuario\Model\Usuario;
use App\Modules\Veiculo\Model\Veiculo;

use App\Modules\OrdemDeServico\Service\Service as OSService;

use App\Modules\OrdemDeServico\Controller\Controller;

use Database\Seeders\DatabaseSeeder;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrdemServicoListagemTest extends TestCase
{
    use RefreshDatabase;

    private $serviceMock;
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(OSService::class);
        $this->controller = new Controller($this->serviceMock);

        $this->assertDatabaseEmpty('os');
        $this->assertDatabaseEmpty('veiculo');
        $this->assertDatabaseEmpty('cliente');
        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('model_has_roles');

        $this->seed(DatabaseSeeder::class);
    }

    public function test_listagem_deve_retornar_erro_500_quando_service_lanca_excecao()
    {
        // Arrange

        $this->serviceMock
            ->shouldReceive('listagem')
            ->once()
            ->andThrow(new Exception('Erro'));

        // Act

        $response = $this->controller->listagem();

        // Assert

        $this->assertEquals(500, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['error']);
        $this->assertEquals('Erro', $responseData['message']);
    }


    public function test_ordens_de_servico_cadastradas_podem_ser_listadas(): void
    {
        // Arrange

        // Act

        $response = $this->getJson('/api/os');

        // Assert

        $response->assertOk();
        $response->assertJsonStructure([
            'data'
        ]);
    }

    public function test_ordem_de_servico_pode_ser_listada_informando_uuid_de_cadastro(): void
    {
        // Arrange

        $cliente = Cliente::factory()->create()->fresh();
        $veiculo = Veiculo::factory()->create()->fresh();

        $atendente = Usuario::factory()->create()->fresh();
        $atendente->assignRole(Papel::ATENDENTE->value);

        $mecanico = Usuario::factory()->create()->fresh();
        $mecanico->assignRole(Papel::MECANICO->value);

        $payload = [
            'cliente_uuid'           => $cliente->uuid,
            'veiculo_uuid'           => $veiculo->uuid,
            'usuario_uuid_atendente' => $atendente->uuid,
            'usuario_uuid_mecanico'  => $mecanico->uuid,
            'descricao'              => 'Motor batendo em baixa rotação',
            'valor_total'            => 1000,
            'valor_desconto'         => 50,
            'prazo_validade'         => 7,
        ];

        // Act

        $response = $this->postJson('/api/os', $payload);

        // Assert

        $response->assertCreated();

        $response->assertJsonStructure([
            'uuid',
        ]);

        $uuidOs = $response->json('uuid');

        $responseOs = $this->getJson('/api/os/' . $uuidOs);
        $responseOs->assertOk();
    }


}
