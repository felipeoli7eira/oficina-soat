<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\UseCase\Material\DeleteUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseTest extends TestCase
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
            sku: null,
            descricao: null,
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

    public function testExecComMaterialNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado com o identificador informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(MaterialGateway::class);

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
        $gateway = $this->createMock(MaterialGateway::class);

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
        $gateway = $this->createMock(MaterialGateway::class);

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
