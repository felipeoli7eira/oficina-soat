<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\UseCase\Servico\UpdateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', ['nome' => 'Novo Nome', 'valor' => 20000])
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'valor' => 20000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', 'Novo Nome', 20000);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-123', $resultado->uuid);
        $this->assertEquals('Novo Nome', $resultado->nome);
        $this->assertEquals(20000, $resultado->valor);
    }

    public function testExecComUuidVazio()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(ServicoGateway::class);
        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('', 'Novo Nome', 20000);
    }

    public function testExecComServicoNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(ServicoGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('uuid-inexistente', 'Novo Nome', 20000);
    }

    public function testExecComDeletadoEmPreenchido()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'valor' => 20000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => '2025-01-03 10:00:00',
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', 'Novo Nome', 20000);

        $this->assertInstanceOf(DateTimeImmutable::class, $resultado->deletadoEm);
    }
}
