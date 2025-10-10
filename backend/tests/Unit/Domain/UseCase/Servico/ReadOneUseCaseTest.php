<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\UseCase\Servico\ReadOneUseCase;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReadOneUseCaseTest extends TestCase
{
    public function testExecComServicoEncontrado()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Troca de óleo',
            valor: 15000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('uuid-123');

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Troca de óleo', $resultado['nome']);
    }

    public function testExecComServicoNaoEncontrado()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new ReadOneUseCase('uuid-inexistente');

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado);
    }

    public function testExecComUuidVazio()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $gateway->expects($this->never())
            ->method('encontrarPorIdentificadorUnico');

        $useCase = new ReadOneUseCase('');

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado);
    }
}
