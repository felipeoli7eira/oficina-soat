<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\RepositorioInterface;
use App\Infrastructure\Gateway\UsuarioGateway;
use PHPUnit\Framework\TestCase;

class UsuarioGatewayTest extends TestCase
{
    public function testEncontrarPorIdentificadorUnico()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $entidadeEsperada = $this->createMock(Entidade::class);

        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('joao@example.com', 'email')
            ->willReturn($entidadeEsperada);

        $gateway = new UsuarioGateway($repositorio);

        $resultado = $gateway->encontrarPorIdentificadorUnico('joao@example.com', 'email');

        $this->assertInstanceOf(Entidade::class, $resultado);
    }

    public function testEncontrarPorIdentificadorUnicoRetornaNull()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('inexistente@example.com', 'email')
            ->willReturn(null);

        $gateway = new UsuarioGateway($repositorio);

        $resultado = $gateway->encontrarPorIdentificadorUnico('inexistente@example.com', 'email');

        $this->assertNull($resultado);
    }

    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $dados = [
            'nome' => 'João Silva',
            'email' => 'joao@example.com',
            'senha' => 'senha123',
            'perfil' => 'atendente',
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'nome' => 'João Silva',
            'email' => 'joao@example.com',
            'perfil' => 'atendente',
        ];

        $repositorio->expects($this->once())
            ->method('criar')
            ->with($dados)
            ->willReturn($retornoEsperado);

        $gateway = new UsuarioGateway($repositorio);

        $resultado = $gateway->criar($dados);

        $this->assertIsArray($resultado);
        $this->assertEquals($retornoEsperado, $resultado);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

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

        $repositorio->expects($this->once())
            ->method('listar')
            ->with([
                'uuid',
                'nome',
                'email',
                'ativo',
                'criado_em',
                'atualizado_em',
            ])
            ->willReturn($usuariosEsperados);

        $gateway = new UsuarioGateway($repositorio);

        $resultado = $gateway->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
        $this->assertEquals($usuariosEsperados, $resultado);
    }

    public function testDeletar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $gateway = new UsuarioGateway($repositorio);

        $resultado = $gateway->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $novosDados = ['nome' => 'Novo Nome'];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'nome' => 'Novo Nome',
            'email' => 'joao@example.com',
        ];

        $repositorio->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', $novosDados)
            ->willReturn($retornoEsperado);

        $gateway = new UsuarioGateway($repositorio);

        $resultado = $gateway->atualizar('uuid-123', $novosDados);

        $this->assertIsArray($resultado);
        $this->assertEquals($retornoEsperado, $resultado);
    }
}
