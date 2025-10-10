<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Usuario;

use App\Domain\UseCase\Usuario\ReadUseCase;
use App\Infrastructure\Gateway\UsuarioGateway;
use PHPUnit\Framework\TestCase;

class ReadUseCaseTest extends TestCase
{
    public function testExecRetornaListaDeUsuarios()
    {
        $gateway = $this->createMock(UsuarioGateway::class);

        $usuariosEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'João Silva',
                'email' => 'joao@example.com',
                'ativo' => true,
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Maria Santos',
                'email' => 'maria@example.com',
                'ativo' => true,
            ],
        ];

        $gateway->expects($this->once())
            ->method('listar')
            ->willReturn($usuariosEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
        $this->assertEquals($usuariosEsperados, $resultado);
    }

    public function testExecRetornaListaVazia()
    {
        $gateway = $this->createMock(UsuarioGateway::class);

        $gateway->expects($this->once())
            ->method('listar')
            ->willReturn([]);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }
}
