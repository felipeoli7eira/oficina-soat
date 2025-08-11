<?php

namespace Tests\Feature\Modules\Cliente;

use App\Modules\Cliente\Requests\CadastroRequest;

use App\Modules\Cliente\Service\Service as ClienteService;

use App\Modules\Cliente\Controller\ClienteController;

use App\Modules\Cliente\Model\Cliente;

use Database\Seeders\DatabaseSeeder;
use DomainException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ClienteCadastroTest extends TestCase
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

    public function test_cliente_pode_ser_cadastrado(): void
    {
        $payload = [
            'nome' => 'João da Silva',
            'cpf' => '12345678901',
            'email' => 'joao.silva@email.com',
            'telefone_movel' => '(11) 99123-4567',
            'cep' => '01153-000',
            'logradouro' => 'Rua Vitorino Carmilo',
            'numero' => '123',
            'bairro' => 'Barra Funda',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
        ];

        $response = $this->withAuth()->postJson('/api/cliente', $payload);

        $response->assertCreated();
    }

    public function test_cliente_nao_pode_ser_cadastrado_sem_dados_obrigatorios(): void
    {
        // Arrange - payload sem campos obrigatórios
        $payload = [
            'nome' => 'João da Silva',
            'email' => 'joao.silva@email.com',
            // 'telefone_movel' => '(11) 99123-4567', // campo obrigatório removido
            'cep' => '01153-000',
            'logradouro' => 'Rua Vitorino Carmilo',
            'bairro' => 'Barra Funda',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
        ];

        // Act
        $response = $this->withAuth()->postJson('/api/cliente', $payload);

        // Assert
        $response->assertBadRequest();
    }

    public function test_cadastro_de_cliente_retorna_erro_de_regra_de_negocio(): void
    {
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $dtoFake = Mockery::mock(\App\Modules\Cliente\Dto\CadastroDto::class);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($dtoFake);

        $domainException = new DomainException('Erro de regra de negócio', 400);

        $this->serviceMock->shouldReceive('cadastro')
            ->with($dtoFake)
            ->once()
            ->andThrow($domainException);

        $response = $this->controller->cadastro($mockRequest);

        $this->assertEquals(500, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
    }

    public function test_cadastro_de_cliente_retorna_erro_interno_em_excecao_generica(): void
    {
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $dtoFake = Mockery::mock(\App\Modules\Cliente\Dto\CadastroDto::class);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($dtoFake);

        $erroGenerico = new Exception('Erro');

        $this->serviceMock->shouldReceive('cadastro')
            ->with($dtoFake)
            ->once()
            ->andThrow($erroGenerico);

        $response = $this->controller->cadastro($mockRequest);

        $this->assertEquals(500, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertEquals('Erro', $data['message']);
    }
}
