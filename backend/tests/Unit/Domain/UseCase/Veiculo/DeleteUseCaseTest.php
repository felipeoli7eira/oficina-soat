<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Veiculo;

use App\Domain\Entity\Veiculo\Entidade;
use App\Domain\UseCase\Veiculo\DeleteUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\VeiculoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(VeiculoGateway::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
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

    public function testExecComVeiculoNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado com o identificador informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(VeiculoGateway::class);

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
        $gateway = $this->createMock(VeiculoGateway::class);

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
        $gateway = $this->createMock(VeiculoGateway::class);

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
