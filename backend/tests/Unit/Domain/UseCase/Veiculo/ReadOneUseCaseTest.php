<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Veiculo;

use App\Domain\Entity\Veiculo\Entidade;
use App\Domain\UseCase\Veiculo\ReadOneUseCase;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReadOneUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $veiculoGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('uuid-123');

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Toyota', $resultado['marca']);
        $this->assertEquals('Corolla', $resultado['modelo']);
        $this->assertEquals('ABC1234', $resultado['placa']);
    }

    public function testExecComUuidVazio()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $veiculoGateway->expects($this->never())
            ->method('encontrarPorIdentificadorUnico');

        $useCase = new ReadOneUseCase('');

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertNull($resultado);
    }

    public function testExecComVeiculoNaoEncontrado()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $veiculoGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new ReadOneUseCase('uuid-inexistente');

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertNull($resultado);
    }

    public function testExecRetornaHttpResponse()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-02 15:30:00')
        );

        $veiculoGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('uuid-123');

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertArrayHasKey('uuid', $resultado);
        $this->assertArrayHasKey('marca', $resultado);
        $this->assertArrayHasKey('modelo', $resultado);
        $this->assertArrayHasKey('placa', $resultado);
        $this->assertArrayHasKey('ano', $resultado);
        $this->assertArrayHasKey('criado_em', $resultado);
        $this->assertArrayHasKey('atualizado_em', $resultado);
    }
}
