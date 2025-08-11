<?php

namespace Tests\Feature\Modules\Cliente;

use App\Modules\Cliente\Requests\ObterUmPorUuidRequest;
use App\Modules\Cliente\Requests\ListagemRequest;

use App\Modules\Cliente\Service\Service as ClienteService;
use App\Modules\Cliente\Controller\ClienteController;

use App\Modules\Cliente\Model\Cliente;

use Database\Seeders\DatabaseSeeder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ClienteListagemTest extends TestCase
{
    use RefreshDatabase;

    private $serviceMock;
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(ClienteService::class);
        $this->controller = new ClienteController($this->serviceMock);

        $this->assertDatabaseEmpty('cliente');

        $this->seed(DatabaseSeeder::class);
    }

    public function test_listagem_deve_retornar_erro_500_quando_service_lanca_excecao(): void
    {
        // Arrange
        $mockRequest = Mockery::mock(ListagemRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $this->serviceMock
            ->shouldReceive('listagem')
            ->once()
            ->andThrow(new Exception('Erro'));

        // Act
        $response = $this->controller->listagem($mockRequest);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['error']);
        $this->assertEquals('Erro', $responseData['message']);
    }

    public function test_clientes_cadastrados_podem_ser_listados(): void
    {
        // Act
        $response = $this->withAuth()->getJson('/api/cliente');

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data'
        ]);
    }

    public function test_cliente_pode_ser_listado_informando_uuid_de_cadastro(): void
    {
        // Arrange
        $payload = [
            'nome' => 'Maria Joaquina',
            'cpf' => '98192899047', // CPF válido
            'email' => 'maria.joaquina@gmail.com',
            'telefone_movel' => '(11) 99123-4567',
            'cep' => '01153-000',
            'logradouro' => 'Rua Vitorino Carmilo',
            'numero' => '10',
            'bairro' => 'Barra Funda',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
        ];

        // Act - Criar o cliente primeiro
        $response = $this->withAuth()->postJson('/api/cliente', $payload);
        $response->assertCreated();

        $response->assertJsonStructure([
            'uuid',
        ]);

        $uuidCliente = $response->json('uuid');

        // Act - Buscar o cliente criado
        $responseCliente = $this->withAuth()->getJson('/api/cliente/' . $uuidCliente);
        $responseCliente->assertOk();
    }

    public function test_metodo_obter_um_por_uuid_no_controller_de_cliente_recupera_not_found_exception(): void
    {
        // Arrange
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldAllowMockingProtectedMethods();
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);

        $modelNotFound = new ModelNotFoundException();
        $modelNotFound->setModel(Cliente::class);

        $this->serviceMock->shouldReceive('obterUmPorUuid')
            ->with($uuidFake)
            ->once()
            ->andThrow($modelNotFound);

        // Act
        $response = $this->controller->obterUmPorUuid($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertNotEmpty($data['message']);
    }

    public function test_metodo_obter_um_por_uuid_no_controller_de_cliente_recupera_exception_generica(): void
    {
        // Arrange
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldAllowMockingProtectedMethods();
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);

        $this->serviceMock->shouldReceive('obterUmPorUuid')
            ->with($uuidFake)
            ->once()
            ->andThrow(new Exception('Erro interno'));

        // Act
        $response = $this->controller->obterUmPorUuid($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertEquals('Erro interno', $data['message']);
    }
}
