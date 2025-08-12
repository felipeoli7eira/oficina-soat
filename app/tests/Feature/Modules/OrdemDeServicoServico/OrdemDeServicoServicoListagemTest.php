<?php

namespace Tests\Feature\Modules\OrdemDeServicoServico;

use App\Modules\OrdemDeServicoServico\Requests\ObterUmPorUuidRequest;

use App\Modules\OrdemDeServicoServico\Service\Service as OSServicoService;
use App\Modules\OrdemDeServicoServico\Controller\Controller;

use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use App\Modules\Servico\Model\Servico;
use App\Modules\OrdemDeServicoServico\Model\OrdemDeServicoServico;

use Database\Seeders\DatabaseSeeder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrdemDeServicoServicoListagemTest extends TestCase
{
    use RefreshDatabase;

    private $serviceMock;
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(OSServicoService::class);
        $this->controller = new Controller($this->serviceMock);

        $this->assertDatabaseEmpty('os');
        $this->assertDatabaseEmpty('os_servico');
        $this->assertDatabaseEmpty('servicos');

        $this->seed(DatabaseSeeder::class);
    }

    public function test_listagem_deve_retornar_erro_500_quando_service_lanca_excecao(): void
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

    public function test_servicos_de_ordem_de_servico_cadastrados_podem_ser_listados(): void
    {
        // Act
        $response = $this->withAuth()->getJson('/api/os-servico');

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data'
        ]);
    }

    public function test_servico_de_ordem_de_servico_pode_ser_listado_informando_uuid_de_cadastro(): void
    {
        // Arrange
        $oss = OrdemDeServicoServico::factory()->create()->fresh();
        // Act - Buscar o serviço criado
        $responseOsServico = $this->withAuth()->getJson('/api/os-servico/' . $oss->uuid);
        $responseOsServico->assertOk();
    }

    public function test_metodo_obter_um_por_uuid_no_controller_de_os_servico_recupera_not_found_exception(): void
    {
        // Arrange
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);
        $mockRequest->shouldReceive('route')->with('uuid')->andReturn($uuidFake);

        $modelNotFound = new ModelNotFoundException();
        $modelNotFound->setModel(OrdemDeServicoServico::class);

        $this->serviceMock->shouldReceive('obterUmPorUuid')
            ->with($uuidFake)
            ->once()
            ->andThrow($modelNotFound);

        // Act
        $response = $this->controller->obterUmPorUuid($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertStringContainsString('Nenhum registro correspondente ao informado', $data['message']);
    }

    public function test_metodo_obter_um_por_uuid_no_controller_de_os_servico_recupera_exception_generica(): void
    {
        // Arrange
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);
        $mockRequest->shouldReceive('route')->with('uuid')->andReturn($uuidFake);

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
