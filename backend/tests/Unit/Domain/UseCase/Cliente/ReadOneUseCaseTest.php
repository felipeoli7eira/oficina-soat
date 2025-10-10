<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Cliente;

use App\Domain\Entity\Cliente\Entidade;
use App\Domain\UseCase\Cliente\ReadOneUseCase;
use App\Infrastructure\Gateway\ClienteGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReadOneUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $entidade = new Entidade(
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
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('uuid-123');

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('João Silva', $resultado['nome']);
        $this->assertEquals('12345678901', $resultado['documento']);
    }

    public function testExecComUuidVazio()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->never())
            ->method('encontrarPorIdentificadorUnico');

        $useCase = new ReadOneUseCase('');

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado);
    }

    public function testExecComClienteNaoEncontrado()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new ReadOneUseCase('uuid-inexistente');

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado);
    }

    public function testExecRetornaHttpResponse()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-02 15:30:00')
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('uuid-123');

        $resultado = $useCase->exec($gateway);

        $this->assertArrayHasKey('uuid', $resultado);
        $this->assertArrayHasKey('nome', $resultado);
        $this->assertArrayHasKey('documento', $resultado);
        $this->assertArrayHasKey('email', $resultado);
        $this->assertArrayHasKey('fone', $resultado);
        $this->assertArrayHasKey('criado_em', $resultado);
        $this->assertArrayHasKey('atualizado_em', $resultado);
    }
}
