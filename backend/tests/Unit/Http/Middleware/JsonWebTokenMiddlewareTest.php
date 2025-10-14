<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\JsonWebTokenMiddleware;
use App\Signature\TokenServiceInterface;
use App\Domain\Entity\Usuario\RepositorioInterface as UsuarioRepositorio;
use App\Domain\Entity\Usuario\Entidade as UsuarioEntidade;
use App\Infrastructure\Dto\JsonWebTokenFragment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use DateTimeImmutable;
use stdClass;

class JsonWebTokenMiddlewareTest extends TestCase
{
    public function testHandleComTokenValido()
    {
        // Arrange
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $usuarioRepositorio = $this->createMock(UsuarioRepositorio::class);
        $request = $this->createMock(Request::class);
        
        $nextRequestCalled = false;
        $nextRequest = function ($req) use (&$nextRequestCalled) {
            $nextRequestCalled = true;
            return new JsonResponse(['success' => true]);
        };

        $claims = new JsonWebTokenFragment(
            sub: 'user-uuid-123',
            iss: 'oficina-soat',
            aud: 'oficina-soat',
            iat: time(),
            exp: time() + 3600,
            nbf: time()
        );

        $usuarioEntidade = new UsuarioEntidade(
            uuid: 'user-uuid-123',
            nome: 'João Silva',
            email: 'joao@test.com',
            senha: 'hashedpassword',
            ativo: true,
            perfil: 'atendente',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $request->expects($this->once())
            ->method('bearerToken')
            ->willReturn('valid-token-123');

        $tokenService->method('validate')
            ->with('valid-token-123')
            ->willReturn($claims);

        $usuarioRepositorio->method('encontrarPorIdentificadorUnico')
            ->with('user-uuid-123', 'uuid')
            ->willReturn($usuarioEntidade);

        $attributesBag = $this->createMock(\Symfony\Component\HttpFoundation\ParameterBag::class);
        $attributesBag->expects($this->once())
            ->method('set')
            ->with('user', $usuarioEntidade);
        
        $request->attributes = $attributesBag;

        // Act
        $middleware = new JsonWebTokenMiddleware($tokenService, $usuarioRepositorio);
        $result = $middleware->handle($request, $nextRequest);

        // Assert
        $this->assertTrue($nextRequestCalled);
        $this->assertInstanceOf(JsonResponse::class, $result);
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testHandleComTokenAusente()
    {
        // Arrange
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $usuarioRepositorio = $this->createMock(UsuarioRepositorio::class);
        $request = $this->createMock(Request::class);
        
        $nextRequest = function ($req) {
            return new JsonResponse(['success' => true]);
        };

        $request->expects($this->once())
            ->method('bearerToken')
            ->willReturn(null);

        // Act
        $middleware = new JsonWebTokenMiddleware($tokenService, $usuarioRepositorio);
        $result = $middleware->handle($request, $nextRequest);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Informe as credenciais de autenticação', $data['msg']);
    }

    public function testHandleComTokenInvalido()
    {
        // Arrange
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $usuarioRepositorio = $this->createMock(UsuarioRepositorio::class);
        $request = $this->createMock(Request::class);
        
        $nextRequest = function ($req) {
            return new JsonResponse(['success' => true]);
        };

        $request->expects($this->once())
            ->method('bearerToken')
            ->willReturn('invalid-token');

        $tokenService->method('validate')
            ->with('invalid-token')
            ->willReturn(null);

        // Act
        $middleware = new JsonWebTokenMiddleware($tokenService, $usuarioRepositorio);
        $result = $middleware->handle($request, $nextRequest);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Token inválido', $data['msg']);
    }

    public function testHandleComUsuarioNaoEncontrado()
    {
        // Arrange
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $usuarioRepositorio = $this->createMock(UsuarioRepositorio::class);
        $request = $this->createMock(Request::class);
        
        $nextRequest = function ($req) {
            return new JsonResponse(['success' => true]);
        };

        $claims = new JsonWebTokenFragment(
            sub: 'non-existent-uuid',
            iss: 'oficina-soat',
            aud: 'oficina-soat',
            iat: time(),
            exp: time() + 3600,
            nbf: time()
        );

        $request->expects($this->once())
            ->method('bearerToken')
            ->willReturn('valid-token-123');

        $tokenService->method('validate')
            ->with('valid-token-123')
            ->willReturn($claims);

        $usuarioRepositorio->method('encontrarPorIdentificadorUnico')
            ->with('non-existent-uuid', 'uuid')
            ->willReturn(null);

        // Act
        $middleware = new JsonWebTokenMiddleware($tokenService, $usuarioRepositorio);
        $result = $middleware->handle($request, $nextRequest);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('É necessário autenticação para acessar este recurso', $data['msg']);
    }

    public function testHandleComTokenVazio()
    {
        // Arrange
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $usuarioRepositorio = $this->createMock(UsuarioRepositorio::class);
        $request = $this->createMock(Request::class);
        
        $nextRequest = function ($req) {
            return new JsonResponse(['success' => true]);
        };

        $request->expects($this->once())
            ->method('bearerToken')
            ->willReturn('');

        // Act
        $middleware = new JsonWebTokenMiddleware($tokenService, $usuarioRepositorio);
        $result = $middleware->handle($request, $nextRequest);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Informe as credenciais de autenticação', $data['msg']);
    }
}