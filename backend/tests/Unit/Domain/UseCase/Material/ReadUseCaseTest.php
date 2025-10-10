<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\UseCase\Material\ReadUseCase;
use App\Infrastructure\Gateway\MaterialGateway;
use PHPUnit\Framework\TestCase;

class ReadUseCaseTest extends TestCase
{
    public function testExecRetornaListaDeMateriais()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $materiaisEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'Óleo de Motor',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => 'OLE-001',
                'descricao' => 'Óleo sintético',
                'preco_custo' => 5000,
                'preco_venda' => 8000,
                'preco_uso_interno' => 6000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Filtro de Ar',
                'gtin' => '7891234567891',
                'estoque' => 50,
                'sku' => 'FIL-001',
                'descricao' => 'Filtro de ar automotivo',
                'preco_custo' => 3000,
                'preco_venda' => 5000,
                'preco_uso_interno' => 4000,
                'criado_em' => '2025-01-02 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
            ],
        ];

        $gateway->expects($this->once())
            ->method('listar')
            ->willReturn($materiaisEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    public function testExecRetornaListaVazia()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->expects($this->once())
            ->method('listar')
            ->willReturn([]);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }

    public function testExecFormataPrecosCorretamente()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $materiaisEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'Óleo de Motor',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => 'OLE-001',
                'descricao' => 'Óleo sintético',
                'preco_custo' => 5050,
                'preco_venda' => 8075,
                'preco_uso_interno' => 6025,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
        ];

        $gateway->method('listar')
            ->willReturn($materiaisEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertEquals(50.5, $resultado[0]['preco_custo']);
        $this->assertEquals(80.75, $resultado[0]['preco_venda']);
        $this->assertEquals(60.25, $resultado[0]['preco_uso_interno']);
    }

    public function testExecFormataMateriaisCorretamente()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $materiaisEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'Óleo de Motor',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => 'OLE-001',
                'descricao' => 'Óleo sintético',
                'preco_custo' => 5000,
                'preco_venda' => 8000,
                'preco_uso_interno' => 6000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
        ];

        $gateway->method('listar')
            ->willReturn($materiaisEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertArrayHasKey('uuid', $resultado[0]);
        $this->assertArrayHasKey('nome', $resultado[0]);
        $this->assertArrayHasKey('gtin', $resultado[0]);
        $this->assertArrayHasKey('estoque', $resultado[0]);
        $this->assertArrayHasKey('sku', $resultado[0]);
        $this->assertArrayHasKey('descricao', $resultado[0]);
    }
}
