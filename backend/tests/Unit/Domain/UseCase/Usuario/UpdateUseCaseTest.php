<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\Perfil;
use App\Domain\UseCase\Usuario\UpdateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\UsuarioGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(UsuarioGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', ['nome' => 'Novo Nome'])
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'email' => 'joao@example.com',
                'senha' => 'senha123',
                'ativo' => true,
                'perfil' => Perfil::ATENDENTE->value,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', ['nome' => 'Novo Nome']);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-123', $resultado->uuid);
        $this->assertEquals('Novo Nome', $resultado->nome);
    }

    public function testExecComUuidVazio()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(UsuarioGateway::class);
        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('', ['nome' => 'Novo Nome']);
    }

    public function testExecComUsuarioNaoEncontrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Usuário não encontrado');
        $this->expectExceptionCode(400);

        $gateway = $this->createMock(UsuarioGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $useCase = new UpdateUseCase($gateway);

        $useCase->exec('uuid-inexistente', ['nome' => 'Novo Nome']);
    }

    public function testExecComDeletadoEmNull()
    {
        $gateway = $this->createMock(UsuarioGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'email' => 'joao@example.com',
                'senha' => 'senha123',
                'ativo' => true,
                'perfil' => Perfil::COMERCIAL->value,
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
        $gateway = $this->createMock(UsuarioGateway::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $gateway->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'email' => 'joao@example.com',
                'senha' => 'senha123',
                'ativo' => false,
                'perfil' => Perfil::ATENDENTE->value,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => '2025-01-03 10:00:00',
            ]);

        $useCase = new UpdateUseCase($gateway);

        $resultado = $useCase->exec('uuid-123', ['nome' => 'Novo Nome']);

        $this->assertInstanceOf(DateTimeImmutable::class, $resultado->deletadoEm);
    }
}
