<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Tests\TestCase;
use App\Http\UsuarioApi;
use App\Infrastructure\Controller\Usuario as UsuarioController;
use App\Domain\Entity\Usuario\RepositorioInterface as UsuarioRepositorio;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use App\Infrastructure\Dto\UsuarioDto;
use App\Exception\DomainHttpException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Mockery;

class UsuarioApiTest extends TestCase
{
    private UsuarioApi $usuarioApi;
    private UsuarioController $mockController;
    private HttpJsonPresenter $presenter;
    private UsuarioRepositorio $mockUsuarioRepositorio;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockController = Mockery::mock(UsuarioController::class);
        $this->presenter = new HttpJsonPresenter(); // Use real instance for final class
        $this->mockUsuarioRepositorio = Mockery::mock(UsuarioRepositorio::class);

        $this->usuarioApi = new UsuarioApi(
            $this->mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );
    }

    // ==================== CREATE TESTS ====================

    public function test_create_com_nome_vazio(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'POST', [
            'nome' => '',
            'email' => 'test@example.com',
            'senha' => 'password123',
            'perfil' => 'admin'
        ]);

        // Act
        $response = $this->usuarioApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_email_vazio(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'POST', [
            'nome' => 'Test User',
            'email' => '',
            'senha' => 'password123',
            'perfil' => 'admin'
        ]);

        // Act
        $response = $this->usuarioApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_email_invalido(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'POST', [
            'nome' => 'Test User',
            'email' => 'invalid-email',
            'senha' => 'password123',
            'perfil' => 'admin'
        ]);

        // Act
        $response = $this->usuarioApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('email', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_senha_vazia(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'POST', [
            'nome' => 'Test User',
            'email' => 'test@example.com',
            'senha' => '',
            'perfil' => 'admin'
        ]);

        // Act
        $response = $this->usuarioApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_perfil_vazio(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'POST', [
            'nome' => 'Test User',
            'email' => 'test@example.com',
            'senha' => 'password123',
            'perfil' => ''
        ]);

        // Act
        $response = $this->usuarioApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_create_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'POST', [
            'nome' => 'Test User',
            'email' => 'test@example.com',
            'senha' => 'password123',
            'perfil' => 'admin'
        ]);

        $mockController = Mockery::mock(UsuarioController::class);
        $mockController->shouldReceive('criar')
            ->with(Mockery::type(UsuarioDto::class), $this->mockUsuarioRepositorio)
            ->andThrow(new DomainHttpException('Email já existe', Response::HTTP_CONFLICT));

        $usuarioApi = new UsuarioApi(
            $mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );

        // Act
        $response = $usuarioApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Email já existe', $responseData['msg']);
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function test_create_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'POST', [
            'nome' => 'Test User',
            'email' => 'test@example.com',
            'senha' => 'password123',
            'perfil' => 'admin'
        ]);

        $mockController = Mockery::mock(UsuarioController::class);
        $mockController->shouldReceive('criar')
            ->andThrow(new Exception('Erro interno'));

        $usuarioApi = new UsuarioApi(
            $mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );

        // Act
        $response = $usuarioApi->create($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    // ==================== READ TESTS ====================

    public function test_read_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'GET');

        $mockController = Mockery::mock(UsuarioController::class);
        $mockController->shouldReceive('listar')
            ->with($this->mockUsuarioRepositorio)
            ->andThrow(new DomainHttpException('Erro ao listar usuários', Response::HTTP_BAD_REQUEST));

        $usuarioApi = new UsuarioApi(
            $mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );

        // Act
        $response = $usuarioApi->read($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro ao listar usuários', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_read_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/usuario', 'GET');

        $mockController = Mockery::mock(UsuarioController::class);
        $mockController->shouldReceive('listar')
            ->with($this->mockUsuarioRepositorio)
            ->andThrow(new Exception('Erro interno'));

        $usuarioApi = new UsuarioApi(
            $mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );

        // Act
        $response = $usuarioApi->read($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    // ==================== UPDATE TESTS ====================

    public function test_update_com_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/usuario/', 'PUT', [
            'nome' => 'Updated User',
            'email' => 'updated@example.com'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '';
                }
            };
        });

        // Act
        $response = $this->usuarioApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_update_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/usuario/invalid-uuid', 'PUT', [
            'nome' => 'Updated User',
            'email' => 'updated@example.com'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        // Act
        $response = $this->usuarioApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_update_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/usuario/123e4567-e89b-12d3-a456-426614174000', 'PUT', [
            'nome' => 'Updated User',
            'email' => 'updated@example.com'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(UsuarioController::class);
        $mockController->shouldReceive('atualizar')
            ->andThrow(new DomainHttpException('Usuário não encontrado', Response::HTTP_NOT_FOUND));

        $usuarioApi = new UsuarioApi(
            $mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );

        // Act
        $response = $usuarioApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Usuário não encontrado', $responseData['msg']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_update_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/usuario/123e4567-e89b-12d3-a456-426614174000', 'PUT', [
            'nome' => 'Updated User',
            'email' => 'updated@example.com'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(UsuarioController::class);
        $mockController->shouldReceive('atualizar')
            ->andThrow(new Exception('Erro interno'));

        $usuarioApi = new UsuarioApi(
            $mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );

        // Act
        $response = $usuarioApi->update($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    // ==================== DELETE TESTS ====================

    public function test_delete_com_uuid_vazio(): void
    {
        // Arrange
        $request = Request::create('/usuario/', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '';
                }
            };
        });

        // Act
        $response = $this->usuarioApi->delete($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('obrigatório', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_delete_com_uuid_invalido(): void
    {
        // Arrange
        $request = Request::create('/usuario/invalid-uuid', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        // Act
        $response = $this->usuarioApi->delete($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertStringContainsString('UUID válido', $responseData['msg']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_delete_com_domain_http_exception(): void
    {
        // Arrange
        $request = Request::create('/usuario/123e4567-e89b-12d3-a456-426614174000', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(UsuarioController::class);
        $mockController->shouldReceive('deletar')
            ->andThrow(new DomainHttpException('Usuário não pode ser excluído', Response::HTTP_CONFLICT));

        $usuarioApi = new UsuarioApi(
            $mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );

        // Act
        $response = $usuarioApi->delete($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Usuário não pode ser excluído', $responseData['msg']);
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function test_delete_com_throwable_generico(): void
    {
        // Arrange
        $request = Request::create('/usuario/123e4567-e89b-12d3-a456-426614174000', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return '123e4567-e89b-12d3-a456-426614174000';
                }
            };
        });

        $mockController = Mockery::mock(UsuarioController::class);
        $mockController->shouldReceive('deletar')
            ->andThrow(new Exception('Erro interno'));

        $usuarioApi = new UsuarioApi(
            $mockController,
            $this->presenter,
            $this->mockUsuarioRepositorio
        );

        // Act
        $response = $usuarioApi->delete($request);

        // Assert
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['err']);
        $this->assertEquals('Erro interno', $responseData['msg']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}