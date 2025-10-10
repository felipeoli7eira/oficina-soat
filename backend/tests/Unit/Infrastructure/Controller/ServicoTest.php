<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Controller;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface;
use App\Infrastructure\Controller\Servico;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ServicoTest extends TestCase
{
    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $repositorio->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Troca de óleo',
                'valor' => 15000,
            ]);

        $controller = new Servico();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->criar('Troca de óleo', 15000);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Troca de óleo', $resultado['nome']);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $servicosEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'Troca de óleo',
                'valor' => 15000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
        ];

        $repositorio->method('listar')
            ->willReturn($servicosEsperados);

        $controller = new Servico();
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
            nome: 'Troca de óleo',
            valor: 15000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $controller = new Servico();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->obterUm('uuid-123');

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
    }

    public function testObterUmRetornaNull()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $controller = new Servico();
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

        $controller = new Servico();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $repositorio->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'valor' => 20000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $controller = new Servico();
        $controller->useRepositorio($repositorio);

        $resultado = $controller->atualizar('uuid-123', 'Novo Nome', 20000);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Novo Nome', $resultado['nome']);
    }

    public function testUseRepositorio()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $controller = new Servico();

        $resultado = $controller->useRepositorio($repositorio);

        $this->assertInstanceOf(Servico::class, $resultado);
        $this->assertSame($repositorio, $resultado->repositorio);
    }
}
