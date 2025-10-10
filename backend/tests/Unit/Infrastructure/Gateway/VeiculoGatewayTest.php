<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Entity\Veiculo\Entidade;
use App\Domain\Entity\Veiculo\RepositorioInterface;
use App\Infrastructure\Gateway\VeiculoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class VeiculoGatewayTest extends TestCase
{
    public function testEncontrarPorIdentificadorUnico()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

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

        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidade);

        $gateway = new VeiculoGateway($repositorio);

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

        $gateway = new VeiculoGateway($repositorio);

        $resultado = $gateway->encontrarPorIdentificadorUnico('uuid-inexistente', 'uuid');

        $this->assertNull($resultado);
    }

    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $dados = [
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'placa' => 'ABC1234',
            'ano' => 2023,
            'cliente_id' => 1,
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'placa' => 'ABC1234',
            'ano' => 2023,
            'cliente_id' => 1,
        ];

        $repositorio->expects($this->once())
            ->method('criar')
            ->with($dados)
            ->willReturn($retornoEsperado);

        $gateway = new VeiculoGateway($repositorio);

        $resultado = $gateway->criar($dados);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Toyota', $resultado['marca']);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $retornoEsperado = [
            [
                'uuid' => 'uuid-1',
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'placa' => 'ABC1234',
                'ano' => 2023,
                'cliente_id' => 1,
            ],
            [
                'uuid' => 'uuid-2',
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'placa' => 'XYZ5678',
                'ano' => 2024,
                'cliente_id' => 2,
            ],
        ];

        $repositorio->expects($this->once())
            ->method('listar')
            ->with(['*'])
            ->willReturn($retornoEsperado);

        $gateway = new VeiculoGateway($repositorio);

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

        $gateway = new VeiculoGateway($repositorio);

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

        $gateway = new VeiculoGateway($repositorio);

        $resultado = $gateway->deletar('uuid-123');

        $this->assertFalse($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $novosDados = [
            'marca' => 'Honda',
            'modelo' => 'Civic',
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'placa' => 'ABC1234',
            'ano' => 2023,
        ];

        $repositorio->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', $novosDados)
            ->willReturn($retornoEsperado);

        $gateway = new VeiculoGateway($repositorio);

        $resultado = $gateway->atualizar('uuid-123', $novosDados);

        $this->assertIsArray($resultado);
        $this->assertEquals('Honda', $resultado['marca']);
        $this->assertEquals('Civic', $resultado['modelo']);
    }

    public function testConstrutorInicializaRepositorio()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $gateway = new VeiculoGateway($repositorio);

        $this->assertSame($repositorio, $gateway->repositorio);
    }
}
