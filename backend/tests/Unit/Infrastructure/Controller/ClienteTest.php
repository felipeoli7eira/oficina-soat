<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Controller;

use App\Domain\Entity\Cliente\Entidade;
use App\Domain\Entity\Cliente\RepositorioInterface;
use App\Infrastructure\Controller\Cliente;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ClienteTest extends TestCase
{
    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $repositorio->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'joao@example.com',
                'fone' => '11999999999',
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ]);

        $controller = new Cliente();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->criar(
            'João Silva',
            '12345678901',
            'joao@example.com',
            '11999999999'
        );

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('João Silva', $resultado['nome']);
        $this->assertEquals('12345678901', $resultado['documento']);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $clientesEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'joao@example.com',
                'fone' => '11999999999',
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Maria Santos',
                'documento' => '98765432109',
                'email' => 'maria@example.com',
                'fone' => '11988888888',
                'criado_em' => '2025-01-02 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
            ],
        ];

        $repositorio->method('listar')
            ->willReturn($clientesEsperados);

        $controller = new Cliente();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    public function testObterUm()
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

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $controller = new Cliente();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->obterUm('uuid-123');

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('João Silva', $resultado['nome']);
    }

    public function testObterUmRetornaNull()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $controller = new Cliente();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->obterUm('uuid-inexistente');

        $this->assertNull($resultado);
    }

    public function testDeletar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $repositorio->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $controller = new Cliente();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $repositorio->method('atualizar')
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

        $controller = new Cliente();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->atualizar('uuid-123', ['nome' => 'João da Silva Santos']);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('João da Silva Santos', $resultado['nome']);
    }

    public function testUseRepositorio()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $controller = new Cliente();

        $resultado = $controller->useRepositorio($repositorio);

        $this->assertInstanceOf(Cliente::class, $resultado);
        $this->assertSame($repositorio, $resultado->repositorio);
    }
}
