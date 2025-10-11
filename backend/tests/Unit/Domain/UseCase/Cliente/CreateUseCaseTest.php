<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Cliente;

use App\Domain\Entity\Cliente\Entidade;
use App\Domain\UseCase\Cliente\CreateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->exactly(3))
            ->method('encontrarPorIdentificadorUnico')
            ->willReturnCallback(function ($valor, $campo) {
                return null;
            });

        $gateway->expects($this->once())
            ->method('criar')
            ->willReturn([
                'uuid' => 'uuid-gerado-123',
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'joao@example.com',
                'fone' => '11999999999',
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ]);

        $useCase = new CreateUseCase(
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999'
        );

        $resultado = $useCase->exec($gateway);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-gerado-123', $resultado->uuid);
        $this->assertEquals('João Silva', $resultado->nome);
        $this->assertEquals('12345678901', $resultado->documento);
        $this->assertEquals('joao@example.com', $resultado->email);
        $this->assertEquals('11999999999', $resultado->fone);
    }

    public function testExecComDocumentoJaCadastrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Cliente com documento repetido');
        $this->expectExceptionCode(400);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('12345678901', 'documento')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999'
        );

        $useCase->exec($gateway);
    }

    public function testExecComFoneJaCadastrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Fone já cadastrado');
        $this->expectExceptionCode(400);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->exactly(2))
            ->method('encontrarPorIdentificadorUnico')
            ->willReturnCallback(function ($valor, $campo) use ($entidadeExistente) {
                if ($campo === 'fone') {
                    return $entidadeExistente;
                }
                return null;
            });

        $gateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999'
        );

        $useCase->exec($gateway);
    }

    public function testExecComEmailJaCadastrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('E-mail já cadastrado');
        $this->expectExceptionCode(400);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->exactly(3))
            ->method('encontrarPorIdentificadorUnico')
            ->willReturnCallback(function ($valor, $campo) use ($entidadeExistente) {
                if ($campo === 'email') {
                    return $entidadeExistente;
                }
                return null;
            });

        $gateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999'
        );

        $useCase->exec($gateway);
    }

    public function testExecComDadosValidos()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $gateway->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Maria Santos',
                'documento' => '98765432109',
                'email' => 'maria@example.com',
                'fone' => '11988888888',
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ]);

        $useCase = new CreateUseCase(
            nome: 'Maria Santos',
            documento: '98765432109',
            email: 'maria@example.com',
            fone: '11988888888'
        );

        $resultado = $useCase->exec($gateway);

        $this->assertEquals('Maria Santos', $resultado->nome);
        $this->assertEquals('98765432109', $resultado->documento);
        $this->assertEquals('maria@example.com', $resultado->email);
        $this->assertEquals('11988888888', $resultado->fone);
    }
}
