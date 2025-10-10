<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class MaterialGatewayTest extends TestCase
{
    public function testEncontrarPorIdentificadorUnico()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidade);

        $gateway = new MaterialGateway($repositorio);

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

        $gateway = new MaterialGateway($repositorio);

        $resultado = $gateway->encontrarPorIdentificadorUnico('uuid-inexistente', 'uuid');

        $this->assertNull($resultado);
    }

    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $dados = [
            'nome' => 'Óleo de Motor',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'sku' => 'OLE-001',
            'descricao' => 'Óleo sintético',
            'preco_custo' => 5000,
            'preco_venda' => 8000,
            'preco_uso_interno' => 6000,
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'nome' => 'Óleo de Motor',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'sku' => 'OLE-001',
            'descricao' => 'Óleo sintético',
            'preco_custo' => 5000,
            'preco_venda' => 8000,
            'preco_uso_interno' => 6000,
        ];

        $repositorio->expects($this->once())
            ->method('criar')
            ->with($dados)
            ->willReturn($retornoEsperado);

        $gateway = new MaterialGateway($repositorio);

        $resultado = $gateway->criar($dados);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Óleo de Motor', $resultado['nome']);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $retornoEsperado = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'Óleo de Motor',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => 'OLE-001',
                'descricao' => 'Óleo sintético',
                'preco_custo' => 5000,
                'preco_venda' => 8000,
                'preco_uso_interno' => 6000,
            ],
        ];

        $repositorio->expects($this->once())
            ->method('listar')
            ->with(['*'])
            ->willReturn($retornoEsperado);

        $gateway = new MaterialGateway($repositorio);

        $resultado = $gateway->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
    }

    public function testDeletar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $gateway = new MaterialGateway($repositorio);

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

        $gateway = new MaterialGateway($repositorio);

        $resultado = $gateway->deletar('uuid-123');

        $this->assertFalse($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $novosDados = [
            'nome' => 'Óleo de Motor Premium',
            'estoque' => 150,
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'nome' => 'Óleo de Motor Premium',
            'gtin' => '7891234567890',
            'estoque' => 150,
            'sku' => 'OLE-001',
            'descricao' => 'Óleo sintético',
            'preco_custo' => 5000,
            'preco_venda' => 8000,
            'preco_uso_interno' => 6000,
        ];

        $repositorio->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', $novosDados)
            ->willReturn($retornoEsperado);

        $gateway = new MaterialGateway($repositorio);

        $resultado = $gateway->atualizar('uuid-123', $novosDados);

        $this->assertIsArray($resultado);
        $this->assertEquals('Óleo de Motor Premium', $resultado['nome']);
        $this->assertEquals(150, $resultado['estoque']);
    }

    public function testConstrutorInicializaRepositorio()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $gateway = new MaterialGateway($repositorio);

        $this->assertSame($repositorio, $gateway->repositorio);
    }
}
