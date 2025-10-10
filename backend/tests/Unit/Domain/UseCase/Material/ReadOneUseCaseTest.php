<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\UseCase\Material\ReadOneUseCase;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReadOneUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético',
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
        $this->assertEquals('Óleo de Motor', $resultado['nome']);
        $this->assertEquals('7891234567890', $resultado['gtin']);
    }

    public function testExecComUuidVazio()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->expects($this->never())
            ->method('encontrarPorIdentificadorUnico');

        $useCase = new ReadOneUseCase('');

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado);
    }

    public function testExecComMaterialNaoEncontrado()
    {
        $gateway = $this->createMock(MaterialGateway::class);

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
        $gateway = $this->createMock(MaterialGateway::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético',
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-02 15:30:00')
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('uuid-123');

        $resultado = $useCase->exec($gateway);

        $this->assertArrayHasKey('uuid', $resultado);
        $this->assertArrayHasKey('nome', $resultado);
        $this->assertArrayHasKey('gtin', $resultado);
        $this->assertArrayHasKey('estoque', $resultado);
        $this->assertArrayHasKey('sku', $resultado);
        $this->assertArrayHasKey('descricao', $resultado);
        $this->assertArrayHasKey('preco_custo', $resultado);
        $this->assertArrayHasKey('preco_venda', $resultado);
        $this->assertArrayHasKey('preco_uso_interno', $resultado);
        $this->assertArrayHasKey('criado_em', $resultado);
        $this->assertArrayHasKey('atualizado_em', $resultado);
    }
}
