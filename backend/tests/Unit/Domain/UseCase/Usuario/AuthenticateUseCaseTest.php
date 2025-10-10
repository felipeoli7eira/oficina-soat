<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\Perfil;
use App\Domain\UseCase\Usuario\AuthenticateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Dto\AuthenticatedDto;
use App\Infrastructure\Gateway\UsuarioGateway;
use App\Signature\AuthServiceInterface;
use App\Signature\TokenServiceInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AuthenticateUseCaseTest extends TestCase
{
    public function testExecComCredenciaisValidas()
    {
        $authService = $this->createMock(AuthServiceInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $gateway = $this->createMock(UsuarioGateway::class);

        $usuario = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: password_hash('senha123', PASSWORD_BCRYPT),
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $authService->expects($this->once())
            ->method('attempt')
            ->with('joao@example.com', 'senha123')
            ->willReturn($usuario);

        $tokenService->expects($this->once())
            ->method('generate')
            ->with([
                'sub' => 'uuid-123',
                'perf' => Perfil::ATENDENTE->value,
            ])
            ->willReturn('token-gerado-123');

        $useCase = new AuthenticateUseCase($authService, $tokenService);

        $resultado = $useCase->exec('joao@example.com', 'senha123', $gateway);

        $this->assertInstanceOf(AuthenticatedDto::class, $resultado);
    }

    public function testExecComCredenciaisInvalidas()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Credenciais inválidas');
        $this->expectExceptionCode(401);

        $authService = $this->createMock(AuthServiceInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $gateway = $this->createMock(UsuarioGateway::class);

        $authService->expects($this->once())
            ->method('attempt')
            ->with('joao@example.com', 'senha_errada')
            ->willReturn(null);

        $tokenService->expects($this->never())
            ->method('generate');

        $useCase = new AuthenticateUseCase($authService, $tokenService);

        $useCase->exec('joao@example.com', 'senha_errada', $gateway);
    }

    public function testExecComUsuarioInexistente()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Credenciais inválidas');
        $this->expectExceptionCode(401);

        $authService = $this->createMock(AuthServiceInterface::class);
        $tokenService = $this->createMock(TokenServiceInterface::class);
        $gateway = $this->createMock(UsuarioGateway::class);

        $authService->expects($this->once())
            ->method('attempt')
            ->with('inexistente@example.com', 'senha123')
            ->willReturn(null);

        $useCase = new AuthenticateUseCase($authService, $tokenService);

        $useCase->exec('inexistente@example.com', 'senha123', $gateway);
    }
}
