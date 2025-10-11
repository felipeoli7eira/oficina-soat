<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Controller;

use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorioInterface;
use App\Domain\Entity\Veiculo\Entidade;
use App\Domain\Entity\Veiculo\RepositorioInterface;
use App\Infrastructure\Controller\Veiculo;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class VeiculoTest extends TestCase
{
    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $clienteRepositorio = $this->createMock(ClienteRepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $clienteExistente = new \App\Domain\Entity\Cliente\Entidade(
            uuid: 'cliente-uuid-123',
            nome: 'Cliente Teste',
            documento: '12345678901',
            email: 'cliente@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $clienteRepositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($clienteExistente);

        $clienteRepositorio->method('obterIdNumerico')
            ->willReturn(1);

        $repositorio->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'placa' => 'ABC1234',
                'ano' => 2023,
                'cliente_id' => 1,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ]);

        $controller = new Veiculo();
        $controller->useRepositorio($repositorio);
        $controller->useClienteRepositorio($clienteRepositorio);

        $resultado = $controller->criar(
            'Toyota',
            'Corolla',
            'ABC1234',
            2023,
            'cliente-uuid-123'
        );

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Toyota', $resultado['marca']);
        $this->assertEquals('Corolla', $resultado['modelo']);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $clienteRepositorio = $this->createMock(ClienteRepositorioInterface::class);

        $veiculosEsperados = [
            [
                'uuid' => 'uuid-1',
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'placa' => 'ABC1234',
                'ano' => 2023,
                'cliente_id' => 1,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
        ];

        $repositorio->method('listar')
            ->willReturn($veiculosEsperados);

        $controller = new Veiculo();
        $controller->useRepositorio($repositorio);
        $controller->useClienteRepositorio($clienteRepositorio);

        $resultado = $controller->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
    }

    public function testObterUm()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $clienteRepositorio = $this->createMock(ClienteRepositorioInterface::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $controller = new Veiculo();
        $controller->useRepositorio($repositorio);
        $controller->useClienteRepositorio($clienteRepositorio);

        $resultado = $controller->obterUm('uuid-123');

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Toyota', $resultado['marca']);
    }

    public function testObterUmRetornaNull()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $clienteRepositorio = $this->createMock(ClienteRepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $controller = new Veiculo();
        $controller->useRepositorio($repositorio);
        $controller->useClienteRepositorio($clienteRepositorio);

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

        $controller = new Veiculo();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $repositorio->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'placa' => 'ABC1234',
                'ano' => 2023,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $controller = new Veiculo();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->atualizar('uuid-123', [
            'marca' => 'Honda',
            'modelo' => 'Civic'
        ]);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Honda', $resultado['marca']);
    }

    public function testUseRepositorio()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $controller = new Veiculo();

        $resultado = $controller->useRepositorio($repositorio);

        $this->assertInstanceOf(Veiculo::class, $resultado);
        $this->assertSame($repositorio, $resultado->repositorio);
    }

    public function testUseClienteRepositorio()
    {
        $clienteRepositorio = $this->createMock(ClienteRepositorioInterface::class);
        $controller = new Veiculo();

        $resultado = $controller->useClienteRepositorio($clienteRepositorio);

        $this->assertInstanceOf(Veiculo::class, $resultado);
        $this->assertSame($clienteRepositorio, $resultado->clienteRepositorio);
    }
}
