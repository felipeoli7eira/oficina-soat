<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\UseCase\Material\UpdateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $entidadeExistente = new Entidade(
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
            atualizadoEm: new DateTimeImmutable('2025-01-01 10:00:00')
        );

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Óleo de Motor Premium',
                'gtin' => '7891234567890',
                'estoque' => 150,
                'sku' => 'OLE-001',
                'descricao' => 'Óleo sintético premium',
                'preco_custo' => 6000,
                'preco_venda' => 9000,
                'preco_uso_interno' => 7000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', [
            'nome' => 'Óleo de Motor Premium',
            'estoque' => 150,
            'preco_venda' => 9000
        ]);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-123', $resultado->uuid);
        $this->assertEquals('Óleo de Motor Premium', $resultado->nome);
        $this->assertEquals(150, $resultado->estoque);
    }

    public function testExecComUuidVazio()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(MaterialGateway::class);
        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('', ['nome' => 'Novo Nome']);
    }

    public function testExecComMaterialNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');
        $this->expectExceptionCode(404);

        $gateway = $this->createMock(MaterialGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('uuid-inexistente', ['nome' => 'Novo Nome']);
    }

    public function testExecComDeletadoEmNull()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => null,
                'descricao' => null,
                'preco_custo' => 5000,
                'preco_venda' => 8000,
                'preco_uso_interno' => 6000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', ['nome' => 'Novo Nome']);

        $this->assertNull($resultado->deletadoEm);
    }

    public function testExecComDeletadoEmPreenchido()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
            deletadoEm: new DateTimeImmutable()
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => null,
                'descricao' => null,
                'preco_custo' => 5000,
                'preco_venda' => 8000,
                'preco_uso_interno' => 6000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => '2025-01-03 10:00:00',
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', ['nome' => 'Novo Nome']);

        $this->assertInstanceOf(DateTimeImmutable::class, $resultado->deletadoEm);
    }

    public function testExecAtualizaMultiplosCampos()
    {
        $gateway = $this->createMock(MaterialGateway::class);

        $entidadeExistente = new Entidade(
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

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Óleo de Motor Premium',
                'gtin' => '7891234567891',
                'estoque' => 150,
                'sku' => 'OLE-002',
                'descricao' => 'Óleo sintético premium',
                'preco_custo' => 6000,
                'preco_venda' => 9000,
                'preco_uso_interno' => 7000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', [
            'nome' => 'Óleo de Motor Premium',
            'gtin' => '7891234567891',
            'estoque' => 150,
            'sku' => 'OLE-002',
            'descricao' => 'Óleo sintético premium',
            'preco_custo' => 6000,
            'preco_venda' => 9000,
            'preco_uso_interno' => 7000
        ]);

        $this->assertEquals('Óleo de Motor Premium', $resultado->nome);
        $this->assertEquals('7891234567891', $resultado->gtin);
        $this->assertEquals(150, $resultado->estoque);
        $this->assertEquals('OLE-002', $resultado->sku);
        $this->assertEquals(6000, $resultado->preco_custo);
        $this->assertEquals(9000, $resultado->preco_venda);
        $this->assertEquals(7000, $resultado->preco_uso_interno);
    }
}
