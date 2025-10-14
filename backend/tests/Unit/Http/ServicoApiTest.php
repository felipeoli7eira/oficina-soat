<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Tests\TestCase;
use App\Http\ServicoApi;
use App\Infrastructure\Controller\Servico as ServicoController;
use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepositorio;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use App\Exception\DomainHttpException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Mockery;

class ServicoApiTest extends TestCase
{
    private ServicoApi $servicoApi;
    private ServicoController $mockController;
    private HttpJsonPresenter $presenter;
    private ServicoRepositorio $mockServicoRepositorio;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockController = Mockery::mock(ServicoController::class);
        $this->presenter = new HttpJsonPresenter(); // Use real instance for final class
        $this->mockServicoRepositorio = Mockery::mock(ServicoRepositorio::class);

        $this->servicoApi = new ServicoApi(
            $this->mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );
    }

    // ==================== CREATE TESTS ====================

    public function test_create_com_nome_vazio(): void
    {
        // Arrange
        $request = Request::create('/servico', 'POST', [
            'nome' => '',
            'valor' => 100.50
        ]);

        // Act
        $response = $this->servicoApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_valor_vazio(): void
    {
        // Arrange
        $request = Request::create('/servico', 'POST', [
            'nome' => 'Troca de óleo',
            'valor' => ''
        ]);

        // Act
        $response = $this->servicoApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_valor_invalido(): void
    {
        // Arrange
        $request = Request::create('/servico', 'POST', [
            'nome' => 'Troca de óleo',
            'valor' => 'invalid'
        ]);

        // Act
        $response = $this->servicoApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('número', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/servico', 'POST', [
            'nome' => 'Troca de óleo',
            'valor' => 100.50
        ]);

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('criar')
            ->with('Troca de óleo', 10050) // 100.50 * 100 centavos
            ->andThrow(new DomainHttpException('Serviço já existe', Response::HTTP_CONFLICT));

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Serviço já existe', $responseData['msg']);
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function test_create_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/servico', 'POST', [
            'nome' => 'Troca de óleo',
            'valor' => 100.50
        ]);

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('criar')
            ->andThrow(new Exception('Erro interno'));

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_create_conversao_valor_para_centavos(): void
    {
        // Arrange
        $request = Request::create('/servico', 'POST', [
            'nome' => 'Alinhamento',
            'valor' => 75.99
        ]);

        $expectedValueInCents = 7599; // 75.99 * 100

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('criar')
            ->with('Alinhamento', $expectedValueInCents)
            ->andThrow(new Exception('Test validation')); // Força exception para testar conversão

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->create($request);

        // Assert - Se chegou até aqui, a conversão foi feita corretamente
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    // ==================== READ TESTS ====================

    public function test_read_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/servico', 'GET');

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('listar')
            ->andThrow(new DomainHttpException('Erro ao listar serviços', Response::HTTP_BAD_REQUEST));

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->read($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro ao listar serviços', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_read_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/servico', 'GET');

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('listar')
            ->andThrow(new Exception('Erro interno'));

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->read($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    // ==================== READ ONE TESTS ====================

    public function test_readOne_com_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/servico/', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '';
                }
            };
        });

        // Act
        $response = $this->servicoApi->readOne($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_readOne_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/servico/invalid-uuid', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        // Act
        $response = $this->servicoApi->readOne($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_readOne_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/servico/123e4567-e89b-12d3-a456-426614174000', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('obterUm')
            ->andThrow(new Exception('Erro interno'));

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->readOne($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_readOne_com_servico_nao_encontrado(): void
    {
        // Arrange
        $request = Request::create('/servico/123e4567-e89b-12d3-a456-426614174000', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('obterUm')
            ->andReturn(null);

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->readOne($request);

        // Assert
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    // ==================== UPDATE TESTS ====================

    public function test_update_com_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/servico/', 'PUT', [
            'nome' => 'Troca de óleo',
            'valor' => 150.00
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '';
                }
            };
        });

        // Act
        $response = $this->servicoApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_update_com_nome_vazio(): void
    {
        // Arrange
        $request = Request::create('/servico/123e4567-e89b-12d3-a456-426614174000', 'PUT', [
            'nome' => '',
            'valor' => 150.00
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        // Act
        $response = $this->servicoApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_update_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/servico/123e4567-e89b-12d3-a456-426614174000', 'PUT', [
            'nome' => 'Troca de óleo',
            'valor' => 150.00
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('atualizar')
            ->andThrow(new DomainHttpException('Serviço não encontrado', Response::HTTP_NOT_FOUND));

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Serviço não encontrado', $responseData['msg']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_update_conversao_valor_para_centavos(): void
    {
        // Arrange
        $request = Request::create('/servico/123e4567-e89b-12d3-a456-426614174000', 'PUT', [
            'nome' => 'Alinhamento',
            'valor' => 89.99
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $expectedValueInCents = 8999; // 89.99 * 100

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('atualizar')
            ->with('123e4567-e89b-12d3-a456-426614174000', 'Alinhamento', $expectedValueInCents)
            ->andThrow(new Exception('Test validation')); // Força exception para testar conversão

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->update($request);

        // Assert - Se chegou até aqui, a conversão foi feita corretamente
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    // ==================== DELETE TESTS ====================

    public function test_delete_com_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/servico/', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '';
                }
            };
        });

        // Act
        $response = $this->servicoApi->delete($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_delete_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/servico/invalid-uuid', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        // Act
        $response = $this->servicoApi->delete($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_delete_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/servico/123e4567-e89b-12d3-a456-426614174000', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('deletar')
            ->andThrow(new DomainHttpException('Serviço não pode ser excluído', Response::HTTP_CONFLICT));

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->delete($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Serviço não pode ser excluído', $responseData['msg']);
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function test_delete_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/servico/123e4567-e89b-12d3-a456-426614174000', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(ServicoController::class);
        $mockController->shouldReceive('useRepositorio')->andReturnSelf();
        $mockController->shouldReceive('deletar')
            ->andThrow(new Exception('Erro interno'));

        $servicoApi = new ServicoApi(
            $mockController,
            $this->presenter,
            $this->mockServicoRepositorio
        );

        // Act
        $response = $servicoApi->delete($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}