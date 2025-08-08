<?php

namespace Tests\Feature\Modules\OS;

use App\Enums\Papel;
use App\Modules\Cliente\Model\Cliente;
use App\Modules\Usuario\Model\Usuario;
use App\Modules\Veiculo\Model\Veiculo;

use App\Modules\OrdemDeServico\Service\Service as OSService;
use App\Modules\OrdemDeServico\Controller\Controller as OSController;

use Database\Seeders\DatabaseSeeder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
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
        $this->controller = new OSController($this->serviceMock);

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

    public function test_metodo_obter_um_por_uuid_no_controller_de_os_recupera_not_found_exception(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->uuid = $uuidFake;

        $modelNotFound = new ModelNotFoundException();
        $modelNotFound->setModel(\App\Modules\OrdemDeServico\Model\OrdemDeServico::class);

        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('obterUmPorUuid')
            ->with($uuidFake)
            ->once()
            ->andThrow($modelNotFound);

        // Act

        $controller = new OSController($mockService);

        $response = $controller->obterUmPorUuid($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertStringContainsString('Nenhum registro correspondente ao informado', $data['message']);
    }

    public function test_metodo_obter_um_por_uuid_no_controller_de_os_recupera_exception_generica(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->uuid = $uuidFake;

        $modelNotFound = new ModelNotFoundException();
        $modelNotFound->setModel(\App\Modules\OrdemDeServico\Model\OrdemDeServico::class);

        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('obterUmPorUuid')
            ->with($uuidFake)
            ->once()
            ->andThrow(Exception::class);

        // Act

        $controller = new OSController($mockService);

        $response = $controller->obterUmPorUuid($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
    }
}
