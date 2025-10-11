<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Cliente;

use App\Domain\Entity\Cliente\Entidade;
use App\Domain\UseCase\Cliente\DeleteUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

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

    public function testExecComClienteNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado com o identificador informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $gateway->expects($this->never())
            ->method('deletar');

        $useCase = new DeleteUseCase($gateway);

        $useCase->exec('uuid-inexistente');
    }

    public function testExecRetornaTrue()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('deletar')
            ->willReturn(true);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('uuid-123');

        $this->assertIsBool($resultado);
        $this->assertTrue($resultado);
    }

    public function testExecRetornaFalse()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('deletar')
            ->willReturn(false);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('uuid-123');

        $this->assertIsBool($resultado);
        $this->assertFalse($resultado);
    }
}
