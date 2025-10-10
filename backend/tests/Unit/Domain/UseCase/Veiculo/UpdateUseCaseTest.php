<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Veiculo;

use App\Domain\Entity\Veiculo\Entidade;
use App\Domain\UseCase\Veiculo\UpdateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\VeiculoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateUseCaseTest extends TestCase
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
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'placa' => 'ABC1234',
                'ano' => 2023,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', [
            'marca' => 'Honda',
            'modelo' => 'Civic'
        ]);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-123', $resultado->uuid);
        $this->assertEquals('Honda', $resultado->marca);
        $this->assertEquals('Civic', $resultado->modelo);
    }

    public function testExecComUuidVazio()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(VeiculoGateway::class);
        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('', ['marca' => 'Honda']);
    }

    public function testExecComVeiculoNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');
        $this->expectExceptionCode(404);

        $gateway = $this->createMock(VeiculoGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('uuid-inexistente', ['marca' => 'Honda']);
    }

    public function testExecComDeletadoEmNull()
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

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'marca' => 'Honda',
                'modelo' => 'Corolla',
                'placa' => 'ABC1234',
                'ano' => 2023,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', ['marca' => 'Honda']);

        $this->assertNull($resultado->deletadoEm);
    }

    public function testExecComDeletadoEmPreenchido()
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
            atualizadoEm: new DateTimeImmutable(),
            deletadoEm: new DateTimeImmutable()
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'marca' => 'Honda',
                'modelo' => 'Corolla',
                'placa' => 'ABC1234',
                'ano' => 2023,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => '2025-01-03 10:00:00',
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', ['marca' => 'Honda']);

        $this->assertInstanceOf(DateTimeImmutable::class, $resultado->deletadoEm);
    }

    public function testExecAtualizaMultiplosCampos()
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

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'placa' => 'XYZ5678',
                'ano' => 2024,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', [
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'placa' => 'XYZ5678',
            'ano' => 2024
        ]);

        $this->assertEquals('Honda', $resultado->marca);
        $this->assertEquals('Civic', $resultado->modelo);
        $this->assertEquals('XYZ5678', $resultado->placa);
        $this->assertEquals(2024, $resultado->ano);
    }
}
