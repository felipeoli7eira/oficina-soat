<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Ordem\Entidade as OrdemEntidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use App\Domain\UseCase\Ordem\CreateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\OrdemGateway;
use App\Infrastructure\Gateway\VeiculoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{

    public function testExecComSucesso()
    {
        $ordemGateway = $this->createMock(OrdemGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);
        $veiculoGateway = $this->createMock(VeiculoGateway::class);

        $cliente = new ClienteEntidade(
            uuid: 'cliente-uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $veiculo = new VeiculoEntidade(
            uuid: 'veiculo-uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2020,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('cliente-uuid-123', 'uuid')
            ->willReturn($cliente);

        $veiculoGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('veiculo-uuid-123', 'uuid')
            ->willReturn($veiculo);

        $ordemGateway->expects($this->once())
            ->method('obterOrdensDoClienteComStatusDiferenteDe')
            ->with('cliente-uuid-123', OrdemEntidade::STATUS_FINALIZADA)
            ->willReturn([]);

        $ordemGateway->expects($this->once())
            ->method('criar')
            ->willReturn([
                'uuid' => 'ordem-uuid-123',
                'descricao' => 'Manutenção preventiva',
                'status' => OrdemEntidade::STATUS_RECEBIDA,
                'dt_abertura' => '2025-01-01 10:00:00',
            ]);

        $useCase = new CreateUseCase(
            clienteUuid: 'cliente-uuid-123',
            veiculoUuid: 'veiculo-uuid-123',
            descricao: 'Manutenção preventiva'
        );

        $resultado = $useCase->exec($ordemGateway, $clienteGateway, $veiculoGateway);

        $this->assertInstanceOf(OrdemEntidade::class, $resultado);
        $this->assertEquals('ordem-uuid-123', $resultado->uuid);
        $this->assertEquals('Manutenção preventiva', $resultado->descricao);
        $this->assertEquals(OrdemEntidade::STATUS_RECEBIDA, $resultado->status);
    }

    public function testExecComClienteNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Cliente não encontrado');
        $this->expectExceptionCode(404);

        $ordemGateway = $this->createMock(OrdemGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);
        $veiculoGateway = $this->createMock(VeiculoGateway::class);

        $clienteGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('cliente-uuid-inexistente', 'uuid')
            ->willReturn(null);

        $veiculoGateway->expects($this->never())
            ->method('encontrarPorIdentificadorUnico');

        $ordemGateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            clienteUuid: 'cliente-uuid-inexistente',
            veiculoUuid: 'veiculo-uuid-123',
            descricao: 'Teste'
        );

        $useCase->exec($ordemGateway, $clienteGateway, $veiculoGateway);
    }

    public function testExecComVeiculoNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Veículo não encontrado');
        $this->expectExceptionCode(404);

        $ordemGateway = $this->createMock(OrdemGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);
        $veiculoGateway = $this->createMock(VeiculoGateway::class);

        $cliente = new ClienteEntidade(
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
            ->willReturn($cliente);

        $veiculoGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('veiculo-uuid-inexistente', 'uuid')
            ->willReturn(null);

        $ordemGateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            clienteUuid: 'cliente-uuid-123',
            veiculoUuid: 'veiculo-uuid-inexistente',
            descricao: 'Teste'
        );

        $useCase->exec($ordemGateway, $clienteGateway, $veiculoGateway);
    }

    public function testExecComClientePossuindoOrdemNaoFinalizada()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Cliente possui 1 ordem(ns) não finalizada(s)');
        $this->expectExceptionCode(400);

        $ordemGateway = $this->createMock(OrdemGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);
        $veiculoGateway = $this->createMock(VeiculoGateway::class);

        $cliente = new ClienteEntidade(
            uuid: 'cliente-uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $veiculo = new VeiculoEntidade(
            uuid: 'veiculo-uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2020,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('cliente-uuid-123', 'uuid')
            ->willReturn($cliente);

        $veiculoGateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('veiculo-uuid-123', 'uuid')
            ->willReturn($veiculo);

        $ordemGateway->expects($this->once())
            ->method('obterOrdensDoClienteComStatusDiferenteDe')
            ->with('cliente-uuid-123', OrdemEntidade::STATUS_FINALIZADA)
            ->willReturn([
                ['uuid' => 'ordem-existente-123', 'status' => OrdemEntidade::STATUS_EM_EXECUCAO]
            ]);

        $ordemGateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            clienteUuid: 'cliente-uuid-123',
            veiculoUuid: 'veiculo-uuid-123',
            descricao: 'Teste'
        );

        $useCase->exec($ordemGateway, $clienteGateway, $veiculoGateway);
    }

    public function testExecComClientePossuindoMultiplasOrdensNaoFinalizadas()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Cliente possui 2 ordem(ns) não finalizada(s)');
        $this->expectExceptionCode(400);

        $ordemGateway = $this->createMock(OrdemGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);
        $veiculoGateway = $this->createMock(VeiculoGateway::class);

        $cliente = new ClienteEntidade(
            uuid: 'cliente-uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $veiculo = new VeiculoEntidade(
            uuid: 'veiculo-uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2020,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($cliente);

        $veiculoGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($veiculo);

        $ordemGateway->method('obterOrdensDoClienteComStatusDiferenteDe')
            ->willReturn([
                ['uuid' => 'ordem-1', 'status' => OrdemEntidade::STATUS_EM_EXECUCAO],
                ['uuid' => 'ordem-2', 'status' => OrdemEntidade::STATUS_RECEBIDA]
            ]);

        $useCase = new CreateUseCase(
            clienteUuid: 'cliente-uuid-123',
            veiculoUuid: 'veiculo-uuid-123',
            descricao: 'Teste'
        );

        $useCase->exec($ordemGateway, $clienteGateway, $veiculoGateway);
    }

    public function testExecComDescricaoNula()
    {
        $ordemGateway = $this->createMock(OrdemGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);
        $veiculoGateway = $this->createMock(VeiculoGateway::class);

        $cliente = new ClienteEntidade(
            uuid: 'cliente-uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $veiculo = new VeiculoEntidade(
            uuid: 'veiculo-uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2020,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($cliente);

        $veiculoGateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($veiculo);

        $ordemGateway->method('obterOrdensDoClienteComStatusDiferenteDe')
            ->willReturn([]);

        $ordemGateway->method('criar')
            ->willReturn([
                'uuid' => 'ordem-uuid-123',
                'descricao' => null,
                'status' => OrdemEntidade::STATUS_RECEBIDA,
                'dt_abertura' => '2025-01-01 10:00:00',
            ]);

        $useCase = new CreateUseCase(
            clienteUuid: 'cliente-uuid-123',
            veiculoUuid: 'veiculo-uuid-123',
            descricao: null
        );

        $resultado = $useCase->exec($ordemGateway, $clienteGateway, $veiculoGateway);

        $this->assertInstanceOf(OrdemEntidade::class, $resultado);
        $this->assertNull($resultado->descricao);
    }
}
