<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Entity\Cliente\Entidade;
use App\Domain\Entity\Cliente\RepositorioInterface;
use App\Infrastructure\Gateway\ClienteGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ClienteGatewayTest extends TestCase
{
    public function testEncontrarPorIdentificadorUnico()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidade);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->encontrarPorIdentificadorUnico('uuid-123', 'uuid');

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-123', $resultado->uuid);
    }

    public function testEncontrarPorIdentificadorUnicoRetornaNull()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-inexistente', 'uuid')
            ->willReturn(null);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->encontrarPorIdentificadorUnico('uuid-inexistente', 'uuid');

        $this->assertNull($resultado);
    }

    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $dados = [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ];

        $repositorio->expects($this->once())
            ->method('criar')
            ->with($dados)
            ->willReturn($retornoEsperado);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->criar($dados);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('João Silva', $resultado['nome']);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $retornoEsperado = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'joao@example.com',
                'fone' => '11999999999',
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Maria Santos',
                'documento' => '98765432109',
                'email' => 'maria@example.com',
                'fone' => '11988888888',
            ],
        ];

        $repositorio->expects($this->once())
            ->method('listar')
            ->with(['*'])
            ->willReturn($retornoEsperado);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    public function testDeletar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function testDeletarRetornaFalse()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('deletar')
            ->with('uuid-123')
            ->willReturn(false);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->deletar('uuid-123');

        $this->assertFalse($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $novosDados = [
            'nome' => 'João da Silva Santos',
            'email' => 'joao.santos@example.com',
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'nome' => 'João da Silva Santos',
            'documento' => '12345678901',
            'email' => 'joao.santos@example.com',
            'fone' => '11999999999',
        ];

        $repositorio->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', $novosDados)
            ->willReturn($retornoEsperado);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->atualizar('uuid-123', $novosDados);

        $this->assertIsArray($resultado);
        $this->assertEquals('João da Silva Santos', $resultado['nome']);
        $this->assertEquals('joao.santos@example.com', $resultado['email']);
    }

    public function testObterIdNumerico()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('obterIdNumerico')
            ->with('uuid-123')
            ->willReturn(1);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->obterIdNumerico('uuid-123');

        $this->assertEquals(1, $resultado);
    }

    public function testObterIdNumericoRetornaMenosUm()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('obterIdNumerico')
            ->with('uuid-inexistente')
            ->willReturn(-1);

        $gateway = new ClienteGateway($repositorio);

        $resultado = $gateway->obterIdNumerico('uuid-inexistente');

        $this->assertEquals(-1, $resultado);
    }

    public function testConstrutorInicializaRepositorio()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $gateway = new ClienteGateway($repositorio);

        $this->assertSame($repositorio, $gateway->repositorio);
    }
}
