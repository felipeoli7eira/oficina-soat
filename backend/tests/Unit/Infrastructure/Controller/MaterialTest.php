<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Controller;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface;
use App\Infrastructure\Controller\Material;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class MaterialTest extends TestCase
{
    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $repositorio->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Óleo de Motor',
                'gtin' => '7891234567890',
                'estoque' => 100,
                'sku' => 'OLE-001',
                'descricao' => 'Óleo sintético',
                'preco_custo' => 5000,
                'preco_venda' => 8000,
                'preco_uso_interno' => 6000,
            ]);

        $controller = new Material();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->criar(
            'Óleo de Motor',
            '7891234567890',
            100,
            5000,
            8000,
            6000,
            'OLE-001',
            'Óleo sintético'
        );

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Óleo de Motor', $resultado['nome']);
        $this->assertEquals('7891234567890', $resultado['gtin']);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $materiaisEsperados = [
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
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
        ];

        $repositorio->method('listar')
            ->willReturn($materiaisEsperados);

        $controller = new Material();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
    }

    public function testObterUm()
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

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $controller = new Material();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->obterUm('uuid-123');

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Óleo de Motor', $resultado['nome']);
    }

    public function testObterUmRetornaNull()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $controller = new Material();
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

        $controller = new Material();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidadeExistente = new Entidade(
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

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $repositorio->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Óleo de Motor Premium',
                'gtin' => '7891234567890',
                'estoque' => 150,
                'sku' => 'OLE-001',
                'descricao' => 'Óleo sintético premium',
                'preco_custo' => 6000,
                'preco_venda' => 9000,
                'preco_uso_interno' => 7000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $controller = new Material();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->atualizar('uuid-123', [
            'nome' => 'Óleo de Motor Premium',
            'estoque' => 150
        ]);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Óleo de Motor Premium', $resultado['nome']);
    }

    public function testUseRepositorio()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $controller = new Material();

        $resultado = $controller->useRepositorio($repositorio);

        $this->assertInstanceOf(Material::class, $resultado);
        $this->assertSame($repositorio, $resultado->repositorio);
    }
}
