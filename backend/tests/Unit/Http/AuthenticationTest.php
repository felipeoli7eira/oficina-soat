<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use App\Http\Authentication;
use App\Infrastructure\Controller\Usuario as UsuarioController;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use App\Domain\UseCase\Usuario\AuthenticateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Dto\AuthenticatedDto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Throwable;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationTest extends TestCase
{
    public function testAuthenticateComEmailVazio()
    {
        // Arrange
        $usuarioController = $this->createMock(UsuarioController::class);
        $presenter = new HttpJsonPresenter(); // Usar instância real pois não é mockada
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['email', 'senha'])
            ->willReturn([
                'email' => '',
                'senha' => 'senha123'
            ]);

        // Mock validator failure
        $this->mockValidator([
            'email' => '',
            'senha' => 'senha123'
        ], false, 'O campo email é obrigatório.');

        // Act
        $authentication = new Authentication($usuarioController, $presenter);
        $result = $authentication->authenticate($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('email', $data['msg']);
    }

    public function testAuthenticateComEmailInvalido()
    {
        // Arrange
        $usuarioController = $this->createMock(UsuarioController::class);
        $presenter = new HttpJsonPresenter();
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['email', 'senha'])
            ->willReturn([
                'email' => 'email-inválido',
                'senha' => 'senha123'
            ]);

        // Mock validator failure
        $this->mockValidator([
            'email' => 'email-inválido',
            'senha' => 'senha123'
        ], false, 'O campo email deve ser um endereço de e-mail válido.');

        // Act
        $authentication = new Authentication($usuarioController, $presenter);
        $result = $authentication->authenticate($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('email', $data['msg']);
    }

    public function testAuthenticateComSenhaVazia()
    {
        // Arrange
        $usuarioController = $this->createMock(UsuarioController::class);
        $presenter = new HttpJsonPresenter();
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['email', 'senha'])
            ->willReturn([
                'email' => 'joao@test.com',
                'senha' => ''
            ]);

        // Mock validator failure
        $this->mockValidator([
            'email' => 'joao@test.com',
            'senha' => ''
        ], false, 'O campo senha é obrigatório.');

        // Act
        $authentication = new Authentication($usuarioController, $presenter);
        $result = $authentication->authenticate($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('senha', $data['msg']);
    }

    public function testAuthenticateComDomainHttpException()
    {
        // Arrange
        $usuarioController = $this->createMock(UsuarioController::class);
        $presenter = new HttpJsonPresenter();
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['email', 'senha'])
            ->willReturn([
                'email' => 'joao@test.com',
                'senha' => 'senha-incorreta'
            ]);

        // Mock validator success
        $this->mockValidator([
            'email' => 'joao@test.com',
            'senha' => 'senha-incorreta'
        ], true);

        // Mock controller throwing exception
        $usuarioController->expects($this->once())
            ->method('authenticate')
            ->with('joao@test.com', 'senha-incorreta', $this->isInstanceOf(AuthenticateUseCase::class))
            ->willThrowException(new DomainHttpException('Credenciais inválidas', Response::HTTP_UNAUTHORIZED));

        // Act
        $authentication = new Authentication($usuarioController, $presenter);
        $result = $authentication->authenticate($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Credenciais inválidas', $data['msg']);
    }

    public function testAuthenticateComThrowableGenerico()
    {
        // Arrange
        $usuarioController = $this->createMock(UsuarioController::class);
        $presenter = new HttpJsonPresenter();
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['email', 'senha'])
            ->willReturn([
                'email' => 'joao@test.com',
                'senha' => 'senha123'
            ]);

        // Mock validator success
        $this->mockValidator([
            'email' => 'joao@test.com',
            'senha' => 'senha123'
        ], true);

        // Mock controller throwing generic exception
        $usuarioController->expects($this->once())
            ->method('authenticate')
            ->with('joao@test.com', 'senha123', $this->isInstanceOf(AuthenticateUseCase::class))
            ->willThrowException(new Exception('Erro interno do servidor'));

        // Act
        $authentication = new Authentication($usuarioController, $presenter);
        $result = $authentication->authenticate($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno do servidor', $data['msg']);
    }

    private function mockValidator(array $data, bool $passes, ?string $errorMessage = null)
    {
        $validator = $this->createMock(\Illuminate\Validation\Validator::class);
        
        if ($passes) {
            $validator->method('fails')->willReturn(false);
            $validator->method('validated')->willReturn($data);
        } else {
            $validator->method('fails')->willReturn(true);
            
            $errors = $this->createMock(\Illuminate\Support\MessageBag::class);
            $errors->method('first')->willReturn($errorMessage);
            $validator->method('errors')->willReturn($errors);
        }
        
        $validator->method('stopOnFirstFailure')->willReturnSelf();

        Validator::shouldReceive('make')
            ->once()
            ->with($data, [
                'email' => ['required', 'string', 'email'],
                'senha' => ['required', 'string'],
            ])
            ->andReturn($validator);
    }
}