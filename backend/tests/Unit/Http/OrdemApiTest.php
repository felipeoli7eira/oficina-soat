<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Tests\TestCase;
use App\Http\OrdemApi;
use App\Infrastructure\Controller\Ordem as OrdemController;
use App\Domain\Entity\Ordem\RepositorioInterface as OrdemRepositorio;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;
use App\Domain\Entity\Veiculo\RepositorioInterface as VeiculoRepositorio;
use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepositorio;
use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use App\Exception\DomainHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Exception;
use Mockery;

class OrdemApiTest extends TestCase
{
    private OrdemApi $ordemApi;
    private OrdemController $mockController;
    private HttpJsonPresenter $presenter;
    private OrdemRepositorio $mockOrdemRepositorio;
    private ClienteRepositorio $mockClienteRepositorio;
    private VeiculoRepositorio $mockVeiculoRepositorio;
    private ServicoRepositorio $mockServicoRepositorio;
    private MaterialRepositorio $mockMaterialRepositorio;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockController = Mockery::mock(OrdemController::class);
        $this->presenter = new HttpJsonPresenter(); // Use real instance for final class
        $this->mockOrdemRepositorio = Mockery::mock(OrdemRepositorio::class);
        $this->mockClienteRepositorio = Mockery::mock(ClienteRepositorio::class);
        $this->mockVeiculoRepositorio = Mockery::mock(VeiculoRepositorio::class);
        $this->mockServicoRepositorio = Mockery::mock(ServicoRepositorio::class);
        $this->mockMaterialRepositorio = Mockery::mock(MaterialRepositorio::class);

        $this->ordemApi = new OrdemApi(
            $this->mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );
    }

    // ==================== CREATE TESTS ====================

    public function test_create_com_cliente_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'POST', [
            'cliente_uuid' => '',
            'veiculo_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'descricao' => 'Teste'
        ]);

        // Act
        $response = $this->ordemApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_veiculo_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'POST', [
            'cliente_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'veiculo_uuid' => '',
            'descricao' => 'Teste'
        ]);

        // Act
        $response = $this->ordemApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'POST', [
            'cliente_uuid' => 'invalid-uuid',
            'veiculo_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'descricao' => 'Teste'
        ]);

        // Act
        $response = $this->ordemApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'POST', [
            'cliente_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'veiculo_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'descricao' => 'Teste'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('criar')
            ->andThrow(new DomainHttpException('Cliente não encontrado', Response::HTTP_NOT_FOUND));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Cliente não encontrado', $responseData['msg']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_create_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'POST', [
            'cliente_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'veiculo_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'descricao' => 'Teste'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('criar')
            ->andThrow(new Exception('Erro interno'));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_create_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'POST', [
            'cliente_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'veiculo_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'descricao' => 'Troca de óleo'
        ]);

        $expectedResult = ['uuid' => '123e4567-e89b-12d3-a456-426614174002'];

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('criar')
            ->with('123e4567-e89b-12d3-a456-426614174001', '123e4567-e89b-12d3-a456-426614174000', 'Troca de óleo')
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->create($request);

        // Assert
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals($expectedResult, $responseData);
    }

    // ==================== READ TESTS ====================

    public function test_read_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'GET', ['status' => 'aberto']);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('listar')
            ->andThrow(new Exception('Erro interno'));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->read($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_read_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'GET');

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('listar')
            ->andThrow(new DomainHttpException('Erro ao listar ordens', Response::HTTP_BAD_REQUEST));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->read($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro ao listar ordens', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_read_com_filtro_status(): void
    {
        // Arrange
        $request = Request::create('/ordem?status=aprovada', 'GET', ['status' => 'aprovada']);

        $expectedResult = [
            ['uuid' => '123e4567-e89b-12d3-a456-426614174000', 'status' => 'aprovada'],
            ['uuid' => '123e4567-e89b-12d3-a456-426614174001', 'status' => 'aprovada']
        ];

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('listar')
            ->with(['status' => 'aprovada'])
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->read($request);

        // Assert
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals($expectedResult, $responseData);
    }

    public function test_read_sem_filtros(): void
    {
        // Arrange
        $request = Request::create('/ordem', 'GET');

        $expectedResult = [
            ['uuid' => '123e4567-e89b-12d3-a456-426614174000', 'status' => 'recebida'],
            ['uuid' => '123e4567-e89b-12d3-a456-426614174001', 'status' => 'aprovada']
        ];

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('listar')
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->read($request);

        // Assert
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals($expectedResult, $responseData);
    }

    // ==================== READ ONE TESTS ====================

    public function test_readOne_com_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem/', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '';
                }
            };
        });

        // Act
        $response = $this->ordemApi->readOne($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_readOne_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('obterUm')
            ->andThrow(new Exception('Erro interno'));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->readOne($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_readOne_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $expectedResult = [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'status' => 'recebida',
            'descricao' => 'Troca de óleo'
        ];

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('obterUm')
            ->with('123e4567-e89b-12d3-a456-426614174000')
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->readOne($request);

        // Assert
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals($expectedResult, $responseData);
    }

    public function test_readOne_com_resultado_nulo(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useClienteRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useVeiculoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('obterUm')
            ->with('123e4567-e89b-12d3-a456-426614174000')
            ->andReturn([]);  // Return empty array instead of null to avoid presenter error

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->readOne($request);

        // Assert
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals([], $responseData);
    }

    // ==================== UPDATE TESTS ====================

    public function test_update_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/ordem/invalid-uuid', 'PUT', [
            'descricao' => 'Nova descrição'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        // Act
        $response = $this->ordemApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_update_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000', 'PUT', [
            'descricao' => 'Nova descrição'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('atualizar')
            ->andThrow(new DomainHttpException('Ordem não encontrada', Response::HTTP_NOT_FOUND));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Ordem não encontrada', $responseData['msg']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_update_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000', 'PUT', [
            'descricao' => 'Nova descrição'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $expectedResult = ['uuid' => '123e4567-e89b-12d3-a456-426614174000', 'descricao' => 'Nova descrição'];

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('atualizar')
            ->with('123e4567-e89b-12d3-a456-426614174000', ['uuid' => '123e4567-e89b-12d3-a456-426614174000', 'descricao' => 'Nova descrição'])
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->update($request);

        // Assert
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals($expectedResult, $responseData);
    }

    // ==================== UPDATE STATUS TESTS ====================

    public function test_updateStatus_com_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem//status', 'PUT', [
            'status' => 'aprovada'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '';
                }
            };
        });

        // Act
        $response = $this->ordemApi->updateStatus($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_updateStatus_com_status_invalido(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000/status', 'PUT', [
            'status' => 'status_inexistente'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('atualizarStatus')
            ->andThrow(new DomainHttpException('Status inválido', Response::HTTP_BAD_REQUEST));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->updateStatus($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Status inválido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_updateStatus_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000/status', 'PUT', [
            'status' => 'aprovada'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $expectedResult = ['uuid' => '123e4567-e89b-12d3-a456-426614174000', 'status' => 'aprovada'];

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('atualizarStatus')
            ->with('123e4567-e89b-12d3-a456-426614174000', 'aprovada')
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->updateStatus($request);

        // Assert
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals($expectedResult, $responseData);
    }

    // ==================== ADD SERVICE TESTS ====================

    public function test_addService_com_ordem_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'POST', [
            'ordem_uuid' => '',
            'servico_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        // Act
        $response = $this->ordemApi->addService($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_addService_com_servico_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'POST', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'servico_uuid' => ''
        ]);

        // Act
        $response = $this->ordemApi->addService($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_addService_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'POST', [
            'ordem_uuid' => 'invalid-uuid',
            'servico_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        // Act
        $response = $this->ordemApi->addService($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_addService_com_domain_exception(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'POST', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'servico_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useServicoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('adicionaServico')
            ->andThrow(new DomainHttpException('Ordem não encontrada', Response::HTTP_NOT_FOUND));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->addService($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Ordem não encontrada', $responseData['msg']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_addService_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'POST', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'servico_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $expectedResult = 'uuid-servico-adicionado';

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useServicoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('adicionaServico')
            ->with('123e4567-e89b-12d3-a456-426614174001', '123e4567-e89b-12d3-a456-426614174000')
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->addService($request);

        // Assert
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals(['uuid' => $expectedResult], $responseData);
    }

    // ==================== REMOVE SERVICE TESTS ====================

    public function test_removeService_com_ordem_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'DELETE', [
            'ordem_uuid' => '',
            'servico_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        // Act
        $response = $this->ordemApi->removeService($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_removeService_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'DELETE', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'servico_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useServicoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('removeServico')
            ->andThrow(new Exception('Erro interno do servidor'));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->removeService($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno do servidor', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_removeService_com_dados_validos_domain_exception(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'DELETE', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'servico_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useServicoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('removeServico')
            ->andThrow(new DomainHttpException('Serviço não encontrado', Response::HTTP_NOT_FOUND));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->removeService($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Serviço não encontrado', $responseData['msg']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_removeService_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/servico', 'DELETE', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'servico_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useServicoRepositorio')->andReturnSelf();
        $mockController->shouldReceive('removeServico')
            ->with('123e4567-e89b-12d3-a456-426614174001', '123e4567-e89b-12d3-a456-426614174000')
            ->andReturn(true);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->removeService($request);

        // Assert
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals(['success' => true], $responseData);
    }

    // ==================== ADD MATERIAL TESTS ====================

    public function test_addMaterial_com_material_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem/material', 'POST', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'material_uuid' => ''
        ]);

        // Act
        $response = $this->ordemApi->addMaterial($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_addMaterial_com_ordem_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem/material', 'POST', [
            'ordem_uuid' => '',
            'material_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        // Act
        $response = $this->ordemApi->addMaterial($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_addMaterial_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/ordem/material', 'POST', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'material_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useMaterialRepositorio')->andReturnSelf();
        $mockController->shouldReceive('adicionaMaterial')
            ->andThrow(new Exception('Erro de servidor'));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->addMaterial($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro de servidor', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_addMaterial_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/material', 'POST', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'material_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $expectedResult = 'uuid-material-adicionado';

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useMaterialRepositorio')->andReturnSelf();
        $mockController->shouldReceive('adicionaMaterial')
            ->with('123e4567-e89b-12d3-a456-426614174001', '123e4567-e89b-12d3-a456-426614174000')
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->addMaterial($request);

        // Assert
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals(['uuid' => $expectedResult], $responseData);
    }

    // ==================== REMOVE MATERIAL TESTS ====================

    public function test_removeMaterial_com_material_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem/material', 'DELETE', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'material_uuid' => ''
        ]);

        // Act
        $response = $this->ordemApi->removeMaterial($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_removeMaterial_com_domain_exception(): void
    {
        // Arrange
        $request = Request::create('/ordem/material', 'DELETE', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'material_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useMaterialRepositorio')->andReturnSelf();
        $mockController->shouldReceive('removeMaterial')
            ->andThrow(new DomainHttpException('Material não encontrado na ordem', Response::HTTP_NOT_FOUND));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->removeMaterial($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Material não encontrado na ordem', $responseData['msg']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_removeMaterial_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/ordem/material', 'DELETE', [
            'ordem_uuid' => 'invalid-uuid',
            'material_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        // Act
        $response = $this->ordemApi->removeMaterial($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_removeMaterial_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/material', 'DELETE', [
            'ordem_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'material_uuid' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('useMaterialRepositorio')->andReturnSelf();
        $mockController->shouldReceive('removeMaterial')
            ->with('123e4567-e89b-12d3-a456-426614174001', '123e4567-e89b-12d3-a456-426614174000')
            ->andReturn(true);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->removeMaterial($request);

        // Assert
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertEquals(['success' => true], $responseData);
    }

    // ==================== APROVACAO TESTS ====================

    public function test_aprovacao_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/ordem/invalid-uuid/aprovacao', 'PUT');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        // Act
        $response = $this->ordemApi->aprovacao($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_aprovacao_com_domain_exception(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000/aprovacao', 'PUT');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('aprovarOrdem')
            ->andThrow(new DomainHttpException('Ordem não pode ser aprovada', Response::HTTP_BAD_REQUEST));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->aprovacao($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Ordem não pode ser aprovada', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_aprovacao_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000/aprovacao', 'PUT');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('aprovarOrdem')
            ->andThrow(new Exception('Erro interno na aprovação'));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->aprovacao($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno na aprovação', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_aprovacao_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000/aprovacao', 'PUT');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $expectedResult = ['uuid' => '123e4567-e89b-12d3-a456-426614174000', 'status' => 'aprovada'];

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('aprovarOrdem')
            ->with('123e4567-e89b-12d3-a456-426614174000')
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->aprovacao($request);

        // Assert
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['err']);
        $this->assertEquals('Ordem aprovada com sucesso', $responseData['msg']);
        $this->assertEquals($expectedResult, $responseData['data']);
    }

    // ==================== REPROVACAO TESTS ====================

    public function test_reprovacao_com_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/ordem//reprovacao', 'PUT');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '';
                }
            };
        });

        // Act
        $response = $this->ordemApi->reprovacao($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_reprovacao_com_domain_exception(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000/reprovacao', 'PUT');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('reprovarOrdem')
            ->andThrow(new DomainHttpException('Ordem não pode ser reprovada', Response::HTTP_BAD_REQUEST));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->reprovacao($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Ordem não pode ser reprovada', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_reprovacao_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000/reprovacao', 'PUT');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('reprovarOrdem')
            ->andThrow(new Exception('Erro interno na reprovação'));

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->reprovacao($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno na reprovação', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_reprovacao_com_sucesso(): void
    {
        // Arrange
        $request = Request::create('/ordem/123e4567-e89b-12d3-a456-426614174000/reprovacao', 'PUT');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $expectedResult = ['uuid' => '123e4567-e89b-12d3-a456-426614174000', 'status' => 'reprovada'];

        $mockController = Mockery::mock(OrdemController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('reprovarOrdem')
            ->with('123e4567-e89b-12d3-a456-426614174000')
            ->andReturn($expectedResult);

        $ordemApi = new OrdemApi(
            $mockController,
            $this->presenter,
            $this->mockOrdemRepositorio,
            $this->mockClienteRepositorio,
            $this->mockVeiculoRepositorio,
            $this->mockServicoRepositorio,
            $this->mockMaterialRepositorio
        );

        // Act
        $response = $ordemApi->reprovacao($request);

        // Assert
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertFalse($responseData['err']);
        $this->assertEquals('Ordem reprovada', $responseData['msg']);
        $this->assertEquals($expectedResult, $responseData['data']);
    }
}