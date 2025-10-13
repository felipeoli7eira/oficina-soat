<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Ordem\Entidade as OrdemEntidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use App\Domain\UseCase\Ordem\DeleteUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseTest extends TestCase
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

        $entidadeExistente = new OrdemEntidade(
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
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('deletar')
            ->with('ordem-uuid-123')
            ->willReturn(true);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123');

        $this->assertTrue($resultado);
    }

    public function testExecComOrdemNaoEncontrada()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado com o identificador informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(OrdemGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('ordem-uuid-inexistente', 'uuid')
            ->willReturn(null);

        $gateway->expects($this->never())
            ->method('deletar');

        $useCase = new DeleteUseCase($gateway);

        $useCase->exec('ordem-uuid-inexistente');
    }

    public function testExecRetornaTrue()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $entidadeExistente = $this->createMock(OrdemEntidade::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('deletar')
            ->willReturn(true);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123');

        $this->assertIsBool($resultado);
        $this->assertTrue($resultado);
    }

    public function testExecRetornaFalse()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $entidadeExistente = $this->createMock(OrdemEntidade::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('deletar')
            ->willReturn(false);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123');

        $this->assertIsBool($resultado);
        $this->assertFalse($resultado);
    }

    public function testExecComOrdemFinalizada()
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
            status: OrdemEntidade::STATUS_FINALIZADA,
            servicos: [],
            materiais: [],
            dtFinalizacao: new DateTimeImmutable()
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('deletar')
            ->with('ordem-uuid-123')
            ->willReturn(true);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123');

        $this->assertTrue($resultado);
    }

    public function testExecComOrdemEmExecucao()
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

        $gateway->expects($this->once())
            ->method('deletar')
            ->willReturn(true);

        $useCase = new DeleteUseCase($gateway);

        $resultado = $useCase->exec('ordem-uuid-123');

        $this->assertTrue($resultado);
    }
}
