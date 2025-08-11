<?php

namespace Tests\Feature\Modules\Cliente;

use App\Modules\Cliente\Requests\ObterUmPorUuidRequest;

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

class ClienteRemocaoTest extends TestCase
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

    public function test_cliente_pode_ser_removido(): void
    {
        // Arrange
        $payload = [
            'nome' => 'João da Silva',
            'cpf' => '11144477735', // CPF válido
            'email' => 'joao.silva@email.com',
            'telefone_movel' => '(11) 99123-4567',
            'cep' => '01153-000',
            'logradouro' => 'Rua Vitorino Carmilo',
            'numero' => '123',
            'bairro' => 'Barra Funda',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
        ];

        // Act - Criar o cliente primeiro
        $response = $this->withAuth()->postJson('/api/cliente', $payload);
        $response->assertCreated();

        // Act - Remover o cliente
        $deleteResponse = $this->withAuth()->delete('/api/cliente/' . $response->json('uuid'));
        $deleteResponse->assertNoContent();
    }

    public function test_cliente_nao_pode_ser_removido_sem_um_uuid_valido(): void
    {
        // Act - Tentar remover com UUID inválido
        $deleteResponse = $this->withAuth()->delete('/api/cliente/qualquer-coisa-nao-sendo-um-uuid-valido');

        // Assert
        $deleteResponse->assertBadRequest();
    }

    public function test_remocao_cliente_inexistente_lanca_model_not_found(): void
    {
        // Arrange
        $uuidFake = 'inexistente-uuid-1234';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldAllowMockingProtectedMethods();
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);

        $this->serviceMock->shouldReceive('remocao')
            ->with($uuidFake)
            ->once()
            ->andThrow(new ModelNotFoundException());

        // Act
        $response = $this->controller->remocao($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['error']);
        $this->assertNotEmpty($responseData['message']);
    }

    public function test_remocao_cliente_lanca_excecao_generica(): void
    {
        // Arrange
        $uuidFake = 'uuid-com-erro-generico';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldAllowMockingProtectedMethods();
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);

        $this->serviceMock->shouldReceive('remocao')
            ->with($uuidFake)
            ->once()
            ->andThrow(new Exception('Erro interno'));

        // Act
        $response = $this->controller->remocao($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertEquals('Erro interno', $data['message']);
    }

    public function test_remocao_cliente_sucesso(): void
    {
        // Arrange
        $uuidValido = 'uuid-valido-12345';
        $expectedResponse = ['message' => 'Removido com sucesso'];

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldAllowMockingProtectedMethods();
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidValido);

        $this->serviceMock->shouldReceive('remocao')
            ->with($uuidValido)
            ->once()
            ->andReturn($expectedResponse);

        // Act
        $response = $this->controller->remocao($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($expectedResponse, $responseData);
    }
}
