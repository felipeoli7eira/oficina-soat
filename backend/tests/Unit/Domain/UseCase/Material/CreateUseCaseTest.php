<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\UseCase\Material\CreateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->expects($this->exactly(3))
            ->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $gateway->expects($this->once())
            ->method('criar')
            ->willReturn([
                'uuid' => 'uuid-gerado-123',
                'nome' => 'Óleo de Motor',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => 'OLE-001',
                'descricao' => 'Óleo sintético',
                'preco_custo' => 5000,
                'preco_venda' => 8000,
                'preco_uso_interno' => 6000,
            ]);

        $useCase = new CreateUseCase(
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético'
        );

        $resultado = $useCase->exec($gateway);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-gerado-123', $resultado->uuid);
        $this->assertEquals('Óleo de Motor', $resultado->nome);
        $this->assertEquals('7891234567890', $resultado->gtin);
        $this->assertEquals(100, $resultado->estoque);
    }

    public function testExecComNomeJaCadastrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Material com nome repetido');
        $this->expectExceptionCode(400);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('Óleo de Motor', 'nome')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000
        );

        $useCase->exec($gateway);
    }

    public function testExecComGtinJaCadastrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('GTIN já cadastrado');
        $this->expectExceptionCode(400);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->expects($this->exactly(2))
            ->method('encontrarPorIdentificadorUnico')
            ->willReturnCallback(function ($valor, $campo) use ($entidadeExistente) {
                if ($campo === 'gtin') {
                    return $entidadeExistente;
                }
                return null;
            });

        $gateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000
        );

        $useCase->exec($gateway);
    }

    public function testExecComSkuJaCadastrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('SKU já cadastrado');
        $this->expectExceptionCode(400);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->expects($this->exactly(3))
            ->method('encontrarPorIdentificadorUnico')
            ->willReturnCallback(function ($valor, $campo) use ($entidadeExistente) {
                if ($campo === 'sku') {
                    return $entidadeExistente;
                }
                return null;
            });

        $gateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001'
        );

        $useCase->exec($gateway);
    }

    public function testExecComSkuVazioNaoValidaSku()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->expects($this->exactly(2))
            ->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $gateway->expects($this->once())
            ->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Óleo de Motor',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => null,
                'descricao' => null,
                'preco_custo' => 5000,
                'preco_venda' => 8000,
                'preco_uso_interno' => 6000,
            ]);

        $useCase = new CreateUseCase(
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null
        );

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado->sku);
    }

    public function testExecComDadosValidos()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $gateway->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Filtro de Ar',
                'gtin' => '7891234567891',
                'estoque' => 50,
                'sku' => 'FIL-001',
                'descricao' => 'Filtro de ar automotivo',
                'preco_custo' => 3000,
                'preco_venda' => 5000,
                'preco_uso_interno' => 4000,
            ]);

        $useCase = new CreateUseCase(
            nome: 'Filtro de Ar',
            gtin: '7891234567891',
            estoque: 50,
            preco_custo: 3000,
            preco_venda: 5000,
            preco_uso_interno: 4000,
            sku: 'FIL-001',
            descricao: 'Filtro de ar automotivo'
        );

        $resultado = $useCase->exec($gateway);

        $this->assertEquals('Filtro de Ar', $resultado->nome);
        $this->assertEquals('7891234567891', $resultado->gtin);
        $this->assertEquals(50, $resultado->estoque);
        $this->assertEquals('FIL-001', $resultado->sku);
    }
}
