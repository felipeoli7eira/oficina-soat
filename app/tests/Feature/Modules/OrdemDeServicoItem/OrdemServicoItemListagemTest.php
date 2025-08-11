<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Modules\OrdemDeServicoItem\Requests\ObterUmPorUuidRequest;
use App\Modules\OrdemDeServicoItem\Requests\ListagemRequest;

use App\Modules\OrdemDeServicoItem\Service\Service as OSItemService;
use App\Modules\OrdemDeServicoItem\Controller\Controller;

use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;

use Database\Seeders\DatabaseSeeder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrdemServicoItemListagemTest extends TestCase
{
    use RefreshDatabase;

    private $serviceMock;
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(OSItemService::class);
        $this->controller = new Controller($this->serviceMock);

        $this->assertDatabaseEmpty('os');
        $this->assertDatabaseEmpty('os_item');
        $this->assertDatabaseEmpty('peca_insumo');

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

    public function test_itens_de_ordem_de_servico_cadastrados_podem_ser_listados(): void
    {
        // Act
        $response = $this->withAuth()->getJson('/api/os-item');

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data'
        ]);
    }

    public function test_item_de_ordem_de_servico_pode_ser_listado_informando_uuid_de_cadastro(): void
    {
        // Arrange
        $os = OrdemDeServico::factory()->create()->fresh();
        $pecainsumo = PecaInsumo::factory()->create()->fresh();

        $payload = [
            'os_uuid'           => $os->uuid,
            'peca_insumo_uuid'  => $pecainsumo->uuid,
            'observacao'        => 'Item conforme solicitado pelo cliente',
            'quantidade'        => 1,
            'valor'             => 1000,
        ];

        // Act - Criar o item primeiro
        $response = $this->withAuth()->postJson('/api/os-item', $payload);
        $response->assertCreated();

        $response->assertJsonStructure([
            'uuid',
        ]);

        $uuidOsItem = $response->json('uuid');

        // Act - Buscar o item criado
        $responseOsItem = $this->getJson('/api/os-item/' . $uuidOsItem);
        $responseOsItem->assertOk();
    }

    public function test_metodo_obter_um_por_uuid_no_controller_de_os_item_recupera_not_found_exception(): void
    {
        // Arrange
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);
        $mockRequest->shouldReceive('route')->with('uuid')->andReturn($uuidFake);

        $modelNotFound = new ModelNotFoundException();
        $modelNotFound->setModel(OrdemDeServicoItem::class);

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

    public function test_metodo_obter_um_por_uuid_no_controller_de_os_item_recupera_exception_generica(): void
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
