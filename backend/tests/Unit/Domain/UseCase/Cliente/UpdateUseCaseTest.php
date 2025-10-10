<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Cliente;

use App\Domain\Entity\Cliente\Entidade;
use App\Domain\UseCase\Cliente\UpdateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-01 10:00:00')
        );

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', $this->callback(function ($dados) {
                return isset($dados['nome']) && $dados['nome'] === 'João da Silva Santos';
            }))
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'João da Silva Santos',
                'documento' => '12345678901',
                'email' => 'joao@example.com',
                'fone' => '11999999999',
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', ['nome' => 'João da Silva Santos']);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-123', $resultado->uuid);
        $this->assertEquals('João da Silva Santos', $resultado->nome);
    }

    public function testExecComUuidVazio()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(ClienteGateway::class);
        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('', ['nome' => 'Novo Nome']);
    }

    public function testExecComClienteNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');
        $this->expectExceptionCode(404);

        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('uuid-inexistente', ['nome' => 'Novo Nome']);
    }

    public function testExecComDeletadoEmNull()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'documento' => '12345678901',
                'email' => 'joao@example.com',
                'fone' => '11999999999',
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
        $gateway = $this->createMock(ClienteGateway::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
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
                'documento' => '12345678901',
                'email' => 'joao@example.com',
                'fone' => '11999999999',
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
        $gateway = $this->createMock(ClienteGateway::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'João da Silva Santos',
                'documento' => '12345678901',
                'email' => 'joao.santos@example.com',
                'fone' => '11988888888',
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', [
            'nome' => 'João da Silva Santos',
            'email' => 'joao.santos@example.com',
            'fone' => '11988888888'
        ]);

        $this->assertEquals('João da Silva Santos', $resultado->nome);
        $this->assertEquals('joao.santos@example.com', $resultado->email);
        $this->assertEquals('11988888888', $resultado->fone);
    }
}
