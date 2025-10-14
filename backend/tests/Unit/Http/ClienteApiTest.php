<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use App\Http\ClienteApi;
use App\Infrastructure\Controller\Cliente as ClienteController;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;
use App\Exception\DomainHttpException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ClienteApiTest extends TestCase
{

    public function testCreateComNomeVazio()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['nome', 'documento', 'email', 'fone'])
            ->willReturn([
                'nome' => '',
                'documento' => '12345678901',
                'email' => 'teste@test.com',
                'fone' => '11999999999'
            ]);

        // Mock validator failure
        $this->mockValidator([
            'nome' => '',
            'documento' => '12345678901',
            'email' => 'teste@test.com',
            'fone' => '11999999999'
        ], false, 'O campo nome é obrigatório.');

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('nome', $data['msg']);
    }

    public function testCreateComEmailInvalido()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['nome', 'documento', 'email', 'fone'])
            ->willReturn([
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'email-inválido',
                'fone' => '11999999999'
            ]);

        // Mock validator failure
        $this->mockValidator([
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'email-inválido',
            'fone' => '11999999999'
        ], false, 'O campo email deve ser um endereço de e-mail válido.');

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('email', $data['msg']);
    }

    public function testCreateComDomainHttpException()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['nome', 'documento', 'email', 'fone'])
            ->willReturn([
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'joao@test.com',
                'fone' => '11999999999'
            ]);

        // Mock validator success
        $this->mockValidator([
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@test.com',
            'fone' => '11999999999'
        ], true);

        // Mock controller throwing exception
        $controller->method('useRepositorio')
            ->with($repositorio)
            ->willReturnSelf();

        $controller->expects($this->once())
            ->method('criar')
            ->with('João Silva', '12345678901', 'joao@test.com', '11999999999')
            ->willThrowException(new DomainHttpException('Cliente já existe', Response::HTTP_BAD_REQUEST));

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Cliente já existe', $data['msg']);
    }

    public function testCreateComThrowableGenerico()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('only')
            ->with(['nome', 'documento', 'email', 'fone'])
            ->willReturn([
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'joao@test.com',
                'fone' => '11999999999'
            ]);

        // Mock validator success
        $this->mockValidator([
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@test.com',
            'fone' => '11999999999'
        ], true);

        // Mock controller throwing generic exception
        $controller->method('useRepositorio')
            ->with($repositorio)
            ->willReturnSelf();

        $controller->expects($this->once())
            ->method('criar')
            ->with('João Silva', '12345678901', 'joao@test.com', '11999999999')
            ->willThrowException(new Exception('Erro interno'));

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno', $data['msg']);
    }

    public function testReadComThrowableGenerico()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        // Mock controller throwing generic exception
        $controller->method('useRepositorio')
            ->with($repositorio)
            ->willReturnSelf();

        $controller->expects($this->once())
            ->method('listar')
            ->willThrowException(new Exception('Erro interno'));

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->read($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno', $data['msg']);
    }

    public function testCastsUpdateComDocumento()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);

        $api = new ClienteApi($controller, $presenter, $repositorio);

        // Act
        $result = $api->castsUpdate([
            'nome' => 'João Silva',
            'documento' => '123.456.789-01',
            'fone' => '(11) 99999-9999',
            'campo_null' => null
        ]);

        // Assert
        $this->assertEquals([
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'fone' => '11999999999'
        ], $result);
    }

    public function testReadOneComUuidVazio()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('merge')
            ->with(['uuid' => ''])
            ->willReturnSelf();

        $request->expects($this->once())
            ->method('only')
            ->with(['uuid'])
            ->willReturn(['uuid' => '']);

        $request->expects($this->once())
            ->method('route')
            ->with('uuid')
            ->willReturn('');

        // Mock validator failure
        $this->mockValidatorReadOne(['uuid' => ''], false, 'O campo uuid é obrigatório.');

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->readOne($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('uuid', $data['msg']);
    }

    public function testReadOneComThrowableGenerico()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $request->expects($this->once())
            ->method('merge')
            ->with(['uuid' => $uuid])
            ->willReturnSelf();

        $request->expects($this->once())
            ->method('only')
            ->with(['uuid'])
            ->willReturn(['uuid' => $uuid]);

        $request->expects($this->once())
            ->method('route')
            ->with('uuid')
            ->willReturn($uuid);

        // Mock validator success
        $this->mockValidatorReadOne(['uuid' => $uuid], true);

        // Mock controller throwing generic exception
        $controller->method('useRepositorio')
            ->with($repositorio)
            ->willReturnSelf();

        $controller->expects($this->once())
            ->method('obterUm')
            ->with($uuid)
            ->willThrowException(new Exception('Erro interno'));

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->readOne($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno', $data['msg']);
    }

    public function testUpdateComUuidInvalido()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $invalidUuid = 'uuid-inválido';

        $request->expects($this->once())
            ->method('merge')
            ->with(['uuid' => $invalidUuid])
            ->willReturnSelf();

        $request->expects($this->once())
            ->method('only')
            ->with(['uuid', 'nome', 'documento', 'email', 'fone'])
            ->willReturn(['uuid' => $invalidUuid, 'nome' => 'João']);

        $request->expects($this->once())
            ->method('route')
            ->with('uuid')
            ->willReturn($invalidUuid);

        // Mock validator failure
        $this->mockValidatorUpdate(['uuid' => $invalidUuid, 'nome' => 'João'], false, 'O campo uuid deve ser um UUID válido.');

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->update($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('uuid', $data['msg']);
    }

    public function testUpdateComDomainHttpException()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $request->expects($this->once())
            ->method('merge')
            ->with(['uuid' => $uuid])
            ->willReturnSelf();

        $request->expects($this->once())
            ->method('only')
            ->with(['uuid', 'nome', 'documento', 'email', 'fone'])
            ->willReturn(['uuid' => $uuid, 'nome' => 'João']);

        $request->expects($this->once())
            ->method('route')
            ->with('uuid')
            ->willReturn($uuid);

        // Mock validator success
        $this->mockValidatorUpdate(['uuid' => $uuid, 'nome' => 'João'], true);

        // Mock controller throwing exception
        $controller->method('useRepositorio')
            ->with($repositorio)
            ->willReturnSelf();

        $controller->expects($this->once())
            ->method('atualizar')
            ->willThrowException(new DomainHttpException('Cliente não encontrado', Response::HTTP_NOT_FOUND));

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->update($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Cliente não encontrado', $data['msg']);
    }

    public function testDeleteComUuidVazio()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $request->expects($this->once())
            ->method('merge')
            ->with(['uuid' => ''])
            ->willReturnSelf();

        $request->expects($this->once())
            ->method('only')
            ->with(['uuid'])
            ->willReturn(['uuid' => '']);

        $request->expects($this->once())
            ->method('route')
            ->with('uuid')
            ->willReturn('');

        // Mock validator failure
        $this->mockValidatorDelete(['uuid' => ''], false, 'O campo uuid é obrigatório.');

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->delete($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('uuid', $data['msg']);
    }

    public function testDeleteComThrowableGenerico()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $request = $this->createMock(Request::class);

        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $request->expects($this->once())
            ->method('merge')
            ->with(['uuid' => $uuid])
            ->willReturnSelf();

        $request->expects($this->once())
            ->method('only')
            ->with(['uuid'])
            ->willReturn(['uuid' => $uuid]);

        $request->expects($this->once())
            ->method('route')
            ->with('uuid')
            ->willReturn($uuid);

        // Mock validator success
        $this->mockValidatorDelete(['uuid' => $uuid], true);

        // Mock controller throwing generic exception
        $controller->method('useRepositorio')
            ->with($repositorio)
            ->willReturnSelf();

        $controller->expects($this->once())
            ->method('deletar')
            ->with($uuid)
            ->willThrowException(new Exception('Erro interno'));

        // Act
        $api = new ClienteApi($controller, $presenter, $repositorio);
        $result = $api->delete($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno', $data['msg']);
    }

    public function testCastsUpdateComFoneFormatado()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);

        $api = new ClienteApi($controller, $presenter, $repositorio);

        // Act
        $result = $api->castsUpdate([
            'nome' => 'João Silva',
            'fone' => '(11) 99999-9999',
            'email' => 'joao@test.com'
        ]);

        // Assert
        $this->assertEquals([
            'nome' => 'João Silva',
            'fone' => '11999999999',
            'email' => 'joao@test.com'
        ], $result);
    }

    public function testCastsUpdateComCamposNulos()
    {
        // Arrange
        $controller = $this->createMock(ClienteController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(ClienteRepositorio::class);

        $api = new ClienteApi($controller, $presenter, $repositorio);

        // Act
        $result = $api->castsUpdate([
            'nome' => 'João Silva',
            'documento' => null,
            'email' => null,
            'fone' => null
        ]);

        // Assert
        $this->assertEquals([
            'nome' => 'João Silva'
        ], $result);
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
                'nome'      => ['required', 'string'],
                'documento' => ['required', 'string'],
                'email'     => ['required', 'string', 'email'],
                'fone'      => ['required', 'string'],
            ])
            ->andReturn($validator);
    }

    private function mockValidatorReadOne(array $data, bool $passes, ?string $errorMessage = null)
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
                'uuid' => ['required', 'string', 'uuid'],
            ])
            ->andReturn($validator);
    }

    private function mockValidatorUpdate(array $data, bool $passes, ?string $errorMessage = null)
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
                'uuid'      => ['required', 'string', 'uuid'],
                'nome'      => ['nullable', 'string'],
                'documento' => ['nullable', 'string'],
                'email'     => ['nullable', 'string', 'email'],
                'fone'      => ['nullable', 'string'],
            ])
            ->andReturn($validator);
    }

    private function mockValidatorDelete(array $data, bool $passes, ?string $errorMessage = null)
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
                'uuid' => ['required', 'string', 'uuid'],
            ])
            ->andReturn($validator);
    }
}