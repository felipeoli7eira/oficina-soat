<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\UseCase\Servico\DeleteUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(ServicoGateway::class);

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

    public function testExecComServicoNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Serviço não encontrado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(ServicoGateway::class);

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
        $gateway = $this->createMock(ServicoGateway::class);

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
