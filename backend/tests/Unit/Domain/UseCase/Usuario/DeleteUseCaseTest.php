<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\UseCase\Usuario\DeleteUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\UsuarioGateway;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(UsuarioGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('uuid-123');

        $this->assertTrue($resultado);
    }

    public function testExecComUsuarioNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Usuário não encontrado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(UsuarioGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $gateway->expects($this->never())
            ->method('deletar');

        $useCase = new DeleteUseCase($gateway);

        $useCase->exec('uuid-inexistente');
    }

    public function testExecRetornaFalseQuandoDeletarFalha()
    {
        $gateway = $this->createMock(UsuarioGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('deletar')
            ->with('uuid-123')
            ->willReturn(false);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('uuid-123');

        $this->assertFalse($resultado);
    }
}
