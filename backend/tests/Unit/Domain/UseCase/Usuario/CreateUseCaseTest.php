<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\Perfil;
use App\Domain\UseCase\Usuario\CreateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\UsuarioGateway;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(UsuarioGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('joao@example.com', 'email')
            ->willReturn(null);

        $gateway->expects($this->once())
            ->method('criar')
            ->willReturn([
                'uuid' => 'uuid-gerado-123',
                'nome' => 'João Silva',
                'email' => 'joao@example.com',
                'perfil' => Perfil::ATENDENTE->value,
            ]);

        $useCase = new CreateUseCase(
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            perfil: Perfil::ATENDENTE->value
        );

        $resultado = $useCase->exec($gateway);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-gerado-123', $resultado->uuid);
        $this->assertEquals('João Silva', $resultado->nome);
        $this->assertEquals('joao@example.com', $resultado->email);
    }

    public function testExecComEmailJaCadastrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('E-mail já cadastrado');
        $this->expectExceptionCode(400);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway = $this->createMock(UsuarioGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('joao@example.com', 'email')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            perfil: Perfil::ATENDENTE->value
        );

        $useCase->exec($gateway);
    }

    public function testExecComDadosValidos()
    {
        $gateway = $this->createMock(UsuarioGateway::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $gateway->method('criar')
            ->willReturn(['uuid' => 'uuid-123']);

        $useCase = new CreateUseCase(
            nome: 'Maria Santos',
            email: 'maria@example.com',
            senha: 'senha456',
            perfil: Perfil::MECANICO->value
        );

        $resultado = $useCase->exec($gateway);

        $this->assertEquals('Maria Santos', $resultado->nome);
        $this->assertEquals('maria@example.com', $resultado->email);
        $this->assertEquals('senha456', $resultado->senha);
        $this->assertEquals(Perfil::MECANICO->value, $resultado->perfil);
        $this->assertTrue($resultado->ativo);
    }
}
