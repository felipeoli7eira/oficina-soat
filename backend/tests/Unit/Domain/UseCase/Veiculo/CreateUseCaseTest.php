<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Veiculo;

use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Veiculo\Entidade;
use App\Domain\UseCase\Veiculo\CreateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $clienteEntidade = new ClienteEntidade(
            uuid: 'cliente-uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('cliente-uuid-123', 'uuid')
            ->willReturn($clienteEntidade);

        $clienteGateway->expects($this->once())
            ->method('obterIdNumerico')
            ->with('cliente-uuid-123')
            ->willReturn(1);

        $veiculoGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('ABC1234', 'placa')
            ->willReturn(null);

        $veiculoGateway->expects($this->once())
            ->method('criar')
            ->willReturn([
                'uuid' => 'uuid-gerado-123',
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'placa' => 'ABC1234',
                'ano' => 2023,
                'cliente_id' => 1,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ]);

        $useCase = new CreateUseCase(
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteUuid: 'cliente-uuid-123'
        );

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-gerado-123', $resultado->uuid);
        $this->assertEquals('Toyota', $resultado->marca);
        $this->assertEquals('Corolla', $resultado->modelo);
        $this->assertEquals('ABC1234', $resultado->placa);
        $this->assertEquals(2023, $resultado->ano);
    }

    public function testExecComClienteUuidVazio()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Cliente dono do veículo não informado');
        $this->expectExceptionCode(400);

        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $useCase = new CreateUseCase(
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteUuid: ''
        );

        $useCase->exec($veiculoGateway, $clienteGateway);
    }

    public function testExecComClienteNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Cliente não encontrado');
        $this->expectExceptionCode(404);

        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $clienteGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('cliente-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new CreateUseCase(
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteUuid: 'cliente-inexistente'
        );

        $useCase->exec($veiculoGateway, $clienteGateway);
    }

    public function testExecComIdNumericoInvalido()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Cliente não encontrado');
        $this->expectExceptionCode(404);

        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $clienteEntidade = new ClienteEntidade(
            uuid: 'cliente-uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($clienteEntidade);

        $clienteGateway->expects($this->once())
            ->method('obterIdNumerico')
            ->willReturn(-1);

        $useCase = new CreateUseCase(
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteUuid: 'cliente-uuid-123'
        );

        $useCase->exec($veiculoGateway, $clienteGateway);
    }

    public function testExecComPlacaJaCadastrada()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Placa não pode ser cadastrada pois já existe um veículo com essa placa');
        $this->expectExceptionCode(400);

        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $clienteEntidade = new ClienteEntidade(
            uuid: 'cliente-uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $veiculoExistente = new Entidade(
            uuid: 'veiculo-uuid-456',
            marca: 'Ford',
            modelo: 'Focus',
            placa: 'ABC1234',
            ano: 2022,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($clienteEntidade);

        $clienteGateway->method('obterIdNumerico')
            ->willReturn(1);

        $veiculoGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('ABC1234', 'placa')
            ->willReturn($veiculoExistente);

        $veiculoGateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteUuid: 'cliente-uuid-123'
        );

        $useCase->exec($veiculoGateway, $clienteGateway);
    }

    public function testExecComDadosValidos()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $clienteEntidade = new ClienteEntidade(
            uuid: 'cliente-uuid-456',
            nome: 'Maria Santos',
            documento: '98765432109',
            email: 'maria@example.com',
            fone: '11988888888',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($clienteEntidade);

        $clienteGateway->method('obterIdNumerico')
            ->willReturn(2);

        $veiculoGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $veiculoGateway->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'placa' => 'XYZ5678',
                'ano' => 2024,
                'cliente_id' => 2,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ]);

        $useCase = new CreateUseCase(
            marca: 'Honda',
            modelo: 'Civic',
            placa: 'XYZ5678',
            ano: 2024,
            clienteUuid: 'cliente-uuid-456'
        );

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertEquals('Honda', $resultado->marca);
        $this->assertEquals('Civic', $resultado->modelo);
        $this->assertEquals('XYZ5678', $resultado->placa);
        $this->assertEquals(2024, $resultado->ano);
        $this->assertEquals(2, $resultado->clienteId);
    }
}
