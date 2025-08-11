<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Modules\OrdemDeServicoItem\Requests\ObterUmPorUuidRequest;

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

class OrdemServicoItemRemocaoTest extends TestCase
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

    public function test_ordem_de_servico_item_pode_ser_removida(): void
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

        // Act - Remover o item
        $deleteResponse = $this->withAuth()->delete('/api/os-item/' . $response->json('uuid'));
        $deleteResponse->assertNoContent();
    }

    public function test_ordem_de_servico_item_nao_pode_ser_removida_sem_um_uuid_valido(): void
    {
        // Act - Tentar remover com UUID inválido
        $deleteResponse = $this->withAuth()->delete('/api/os-item/qualquer-coisa-nao-sendo-um-uuid-valido');

        // Assert
        $deleteResponse->assertBadRequest();
    }

    public function test_remocao_os_item_inexistente_lanca_model_not_found(): void
    {
        // Arrange
        $uuidFake = 'inexistente-uuid-1234';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);
        $mockRequest->shouldReceive('route')->with('uuid')->andReturn($uuidFake);

        $this->serviceMock->shouldReceive('remocao')
            ->with($uuidFake)
            ->once()
            ->andThrow(new ModelNotFoundException());

        // Act
        $response = $this->controller->remocao($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['error']);
        $this->assertEquals('Nenhum registro correspondente ao informado', $responseData['message']);
    }

    public function test_remocao_os_item_lanca_excecao_generica(): void
    {
        // Arrange
        $uuidFake = 'uuid-com-erro-generico';

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);
        $mockRequest->shouldReceive('route')->with('uuid')->andReturn($uuidFake);

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

    public function test_remocao_os_item_sucesso(): void
    {
        // Arrange
        $uuidValido = 'uuid-valido-12345';
        $expectedResponse = ['message' => 'Removido com sucesso'];

        $mockRequest = Mockery::mock(ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->andReturn($uuidValido);
        $mockRequest->shouldReceive('route')->with('uuid')->andReturn($uuidValido);

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
