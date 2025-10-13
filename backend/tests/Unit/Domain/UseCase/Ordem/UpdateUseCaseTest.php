<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Ordem\Entidade as OrdemEntidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use App\Domain\UseCase\Ordem\UpdateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateUseCaseTest extends TestCase
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
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-01 10:00:00')
        );

        $veiculo = new VeiculoEntidade(
            uuid: 'veiculo-uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2020,
            clienteId: 1,
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-01 10:00:00')
        );

        $entidadeExistente = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable('2025-01-01 10:00:00'),
            descricao: 'Manutenção preventiva',
            status: OrdemEntidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: []
        );

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('ordem-uuid-123', 'uuid')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('atualizar')
            ->with('ordem-uuid-123', $this->callback(function ($dados) {
                return isset($dados['descricao']) && $dados['descricao'] === 'Manutenção completa';
            }))
            ->willReturn([
                'uuid' => 'ordem-uuid-123',
                'cliente' => [
                    'uuid' => 'cliente-uuid-123',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-123',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'descricao' => 'Manutenção completa',
                'status' => OrdemEntidade::STATUS_RECEBIDA,
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => '2025-01-02 10:00:00',
                'servicos' => [],
                'materiais' => [],
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123', ['descricao' => 'Manutenção completa']);

        $this->assertInstanceOf(OrdemEntidade::class, $resultado);
        $this->assertEquals('ordem-uuid-123', $resultado->uuid);
        $this->assertEquals('Manutenção completa', $resultado->descricao);
    }

    public function testExecComUuidVazio()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(OrdemGateway::class);
        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('', ['descricao' => 'Nova descrição']);
    }

    public function testExecComOrdemNaoEncontrada()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');
        $this->expectExceptionCode(404);

        $gateway = $this->createMock(OrdemGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('ordem-uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('ordem-uuid-inexistente', ['descricao' => 'Nova descrição']);
    }

    public function testExecAtualizaStatus()
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

        $entidadeExistente = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            descricao: 'Manutenção',
            status: OrdemEntidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: []
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'ordem-uuid-123',
                'cliente' => [
                    'uuid' => 'cliente-uuid-123',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-123',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'descricao' => 'Manutenção',
                'status' => OrdemEntidade::STATUS_EM_EXECUCAO,
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => '2025-01-02 10:00:00',
                'servicos' => [],
                'materiais' => [],
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123', ['status' => OrdemEntidade::STATUS_EM_EXECUCAO]);

        $this->assertEquals(OrdemEntidade::STATUS_EM_EXECUCAO, $resultado->status);
    }

    public function testExecAtualizaMultiplosCampos()
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

        $entidadeExistente = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            descricao: 'Manutenção',
            status: OrdemEntidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: []
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'ordem-uuid-123',
                'cliente' => [
                    'uuid' => 'cliente-uuid-123',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-123',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'descricao' => 'Manutenção completa com troca de óleo',
                'status' => OrdemEntidade::STATUS_EM_EXECUCAO,
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => '2025-01-02 10:00:00',
                'servicos' => [],
                'materiais' => [],
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123', [
            'descricao' => 'Manutenção completa com troca de óleo',
            'status' => OrdemEntidade::STATUS_EM_EXECUCAO
        ]);

        $this->assertEquals('Manutenção completa com troca de óleo', $resultado->descricao);
        $this->assertEquals(OrdemEntidade::STATUS_EM_EXECUCAO, $resultado->status);
    }

    public function testExecComDataFinalizacao()
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

        $entidadeExistente = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            descricao: 'Manutenção',
            status: OrdemEntidade::STATUS_EM_EXECUCAO,
            servicos: [],
            materiais: []
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'ordem-uuid-123',
                'cliente' => [
                    'uuid' => 'cliente-uuid-123',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-123',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'descricao' => 'Manutenção',
                'status' => OrdemEntidade::STATUS_FINALIZADA,
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => '2025-01-03 10:00:00',
                'dt_atualizacao' => '2025-01-03 10:00:00',
                'servicos' => [],
                'materiais' => [],
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123', ['status' => OrdemEntidade::STATUS_FINALIZADA]);

        $this->assertEquals(OrdemEntidade::STATUS_FINALIZADA, $resultado->status);
        $this->assertInstanceOf(DateTimeImmutable::class, $resultado->dtFinalizacao);
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

        $entidadeExistente = new OrdemEntidade(
            uuid: 'ordem-uuid-123',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            descricao: 'Manutenção',
            status: OrdemEntidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: []
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'ordem-uuid-123',
                'cliente' => [
                    'uuid' => 'cliente-uuid-123',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-123',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                    'deletado_em' => null,
                ],
                'descricao' => 'Manutenção',
                'status' => OrdemEntidade::STATUS_EM_EXECUCAO,
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => '2025-01-02 10:00:00',
                'servicos' => [
                    [
                        'uuid' => 'servico-uuid-1',
                        'nome' => 'Troca de óleo',
                        'valor' => 15000,
                    ]
                ],
                'materiais' => [
                    [
                        'uuid' => 'material-uuid-1',
                        'nome' => 'Óleo 5W30',
                        'preco_uso_interno' => 10000,
                    ]
                ],
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123', ['status' => OrdemEntidade::STATUS_EM_EXECUCAO]);

        $this->assertIsArray($resultado->servicos);
        $this->assertIsArray($resultado->materiais);
        $this->assertCount(1, $resultado->servicos);
        $this->assertCount(1, $resultado->materiais);
    }
}
