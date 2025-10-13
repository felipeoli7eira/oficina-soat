<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Ordem\Entidade as OrdemEntidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use App\Domain\UseCase\Ordem\ReadOneUseCase;
use App\Infrastructure\Gateway\OrdemGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReadOneUseCaseTest extends TestCase
{

    public function testExecComSucesso()
    {
        $gateway = $this->createMock(OrdemGateway::class);

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

        $entidade = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            descricao: 'Manutenção preventiva',
            status: OrdemEntidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: []
        );

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('ordem-uuid-123', 'uuid')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('ordem-uuid-123');

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertEquals('ordem-uuid-123', $resultado['uuid']);
        $this->assertEquals('Manutenção preventiva', $resultado['descricao']);
        $this->assertEquals('RECEBIDA', $resultado['status']);
    }

    public function testExecComUuidVazio()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $gateway->expects($this->never())
            ->method('encontrarPorIdentificadorUnico');

        $useCase = new ReadOneUseCase('');

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado);
    }

    public function testExecComOrdemNaoEncontrada()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('ordem-uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new ReadOneUseCase('ordem-uuid-inexistente');

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado);
    }

    public function testExecRetornaHttpResponse()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $cliente = new ClienteEntidade(
            uuid: 'cliente-uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-02 15:30:00')
        );

        $veiculo = new VeiculoEntidade(
            uuid: 'veiculo-uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2020,
            clienteId: 1,
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-02 15:30:00')
        );

        $entidade = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable('2025-01-01 10:00:00'),
            descricao: 'Manutenção preventiva',
            status: OrdemEntidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: [],
            dtFinalizacao: null,
            dtAtualizacao: new DateTimeImmutable('2025-01-02 15:30:00')
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('ordem-uuid-123');

        $resultado = $useCase->exec($gateway);

        $this->assertArrayHasKey('uuid', $resultado);
        $this->assertArrayHasKey('cliente', $resultado);
        $this->assertArrayHasKey('veiculo', $resultado);
        $this->assertArrayHasKey('descricao', $resultado);
        $this->assertArrayHasKey('status', $resultado);
        $this->assertArrayHasKey('servicos', $resultado);
        $this->assertArrayHasKey('materiais', $resultado);
        $this->assertArrayHasKey('dt_abertura', $resultado);
        $this->assertArrayHasKey('dt_finalizacao', $resultado);
        $this->assertArrayHasKey('dt_atualizacao', $resultado);
    }

    public function testExecComServicosEMateriais()
    {
        $gateway = $this->createMock(OrdemGateway::class);

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

        $entidade = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            descricao: 'Manutenção completa',
            status: OrdemEntidade::STATUS_EM_EXECUCAO,
            servicos: [
                [
                    'uuid' => 'servico-uuid-1',
                    'nome' => 'Troca de óleo',
                    'valor' => 15000,
                ]
            ],
            materiais: [
                [
                    'uuid' => 'material-uuid-1',
                    'nome' => 'Óleo 5W30',
                    'preco_uso_interno' => 10000,
                ]
            ]
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('ordem-uuid-123');

        $resultado = $useCase->exec($gateway);

        $this->assertArrayHasKey('servicos', $resultado);
        $this->assertArrayHasKey('materiais', $resultado);
        $this->assertArrayHasKey('total_servicos', $resultado);
        $this->assertArrayHasKey('total_materiais', $resultado);
        $this->assertArrayHasKey('total_geral', $resultado);
        $this->assertEquals(150.00, $resultado['total_servicos']);
        $this->assertEquals(100.00, $resultado['total_materiais']);
        $this->assertEquals(250.00, $resultado['total_geral']);
    }

    public function testExecComDataFinalizacaoNull()
    {
        $gateway = $this->createMock(OrdemGateway::class);

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

        $entidade = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            descricao: 'Manutenção',
            status: OrdemEntidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: [],
            dtFinalizacao: null
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $useCase = new ReadOneUseCase('ordem-uuid-123');

        $resultado = $useCase->exec($gateway);

        $this->assertNull($resultado['dt_finalizacao']);
    }
}
