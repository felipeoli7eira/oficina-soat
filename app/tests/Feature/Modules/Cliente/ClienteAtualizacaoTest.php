<?php

namespace Tests\Feature\Modules\Cliente;

use App\Enums\Papel;
use App\Modules\Cliente\Requests\AtualizacaoRequest;

use App\Modules\Cliente\Service\Service as ClienteService;

use App\Modules\Cliente\Controller\ClienteController;

use App\Modules\Cliente\Model\Cliente;

use Database\Seeders\DatabaseSeeder;
use DomainException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ClienteAtualizacaoTest extends TestCase
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

    public function test_cliente_pode_ser_atualizado(): void
    {
        // Arrange
        $cliente = Cliente::factory()->create()->fresh();

        $payloadAtualizacao = [
            'nome' => 'Nome Atualizado',
            'cpf' => $cliente->cpf,
            'email' => 'email.atualizado@teste.com',
            'telefone_movel' => '(11) 99999-9999',
        ];

        // Act
        $response = $this->withAuth()->putJson('/api/cliente/' . $cliente->uuid, $payloadAtualizacao);

        // Assert
        $response->assertOk();
    }

    public function test_nome_do_cliente_pode_ser_atualizado(): void
    {
        // Arrange
        $cliente = Cliente::factory()->create()->fresh();

        $payloadAtualizacao = [
            'nome' => 'Novo Nome do Cliente',
            'cpf' => $cliente->cpf,
        ];

        // Act
        $response = $this->withAuth()->putJson('/api/cliente/' . $cliente->uuid, $payloadAtualizacao);

        // Assert
        $response->assertOk();
    }

    public function test_endereco_do_cliente_pode_ser_atualizado(): void
    {
        // Arrange
        $cliente = Cliente::factory()->create()->fresh();

        $payloadAtualizacao = [
            'cpf' => $cliente->cpf,
            'logradouro' => 'Nova Rua Exemplo',
            'numero' => '456',
            'bairro' => 'Novo Bairro',
            'cidade' => 'Nova Cidade',
            'uf' => 'RJ',
            'cep' => '20000-000',
        ];

        // Act
        $response = $this->withAuth()->putJson('/api/cliente/' . $cliente->uuid, $payloadAtualizacao);

        // Assert
        $response->assertOk();
    }

    public function test_atualizacao_cliente_nao_encontrado_lanca_model_not_found_exception(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockDto = Mockery::mock(\App\Modules\Cliente\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->shouldReceive('route')->with('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $this->serviceMock->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        // Act
        $response = $this->controller->atualizacao($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['error']);
        $this->assertNotEmpty($responseData['message']);
    }

    public function test_atualizacao_cliente_retorna_erro_de_regra_de_negocio(): void
    {
        $uuidFake = 'uuid-valido-1234';

        $mockDto = Mockery::mock(\App\Modules\Cliente\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('route')->with('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $domainException = new DomainException('Erro de regra de negócio', 400);

        $this->serviceMock->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow($domainException);

        // Act
        $response = $this->controller->atualizacao($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['error']);
    }

    public function test_atualizacao_cliente_retorna_erro_interno_em_excecao_generica(): void
    {
        $uuidFake = 'uuid-valido-1234';

        $mockDto = Mockery::mock(\App\Modules\Cliente\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('route')->with('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $erroGenerico = new Exception('Erro interno');

        $this->serviceMock->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow($erroGenerico);

        // Act
        $response = $this->controller->atualizacao($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertEquals('Erro interno', $data['message']);
    }
}
