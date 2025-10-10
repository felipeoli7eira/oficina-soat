<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface;
use App\Infrastructure\Gateway\ServicoGateway;
use PHPUnit\Framework\TestCase;

class ServicoGatewayTest extends TestCase
{
    public function testEncontrarPorIdentificadorUnico()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $entidadeEsperada = $this->createMock(Entidade::class);

        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('Troca de óleo', 'nome')
            ->willReturn($entidadeEsperada);

        $gateway = new ServicoGateway($repositorio);

        $resultado = $gateway->encontrarPorIdentificadorUnico('Troca de óleo', 'nome');

        $this->assertInstanceOf(Entidade::class, $resultado);
    }

    public function testEncontrarPorIdentificadorUnicoRetornaNull()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('Serviço inexistente', 'nome')
            ->willReturn(null);

        $gateway = new ServicoGateway($repositorio);

        $resultado = $gateway->encontrarPorIdentificadorUnico('Serviço inexistente', 'nome');

        $this->assertNull($resultado);
    }

    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $dados = [
            'nome' => 'Troca de óleo',
            'valor' => 15000,
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'nome' => 'Troca de óleo',
            'valor' => 15000,
        ];

        $repositorio->expects($this->once())
            ->method('criar')
            ->with($dados)
            ->willReturn($retornoEsperado);

        $gateway = new ServicoGateway($repositorio);

        $resultado = $gateway->criar($dados);

        $this->assertIsArray($resultado);
        $this->assertEquals($retornoEsperado, $resultado);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $servicosEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'Troca de óleo',
                'valor' => 15000,
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Alinhamento',
                'valor' => 12000,
            ],
        ];

        $repositorio->expects($this->once())
            ->method('listar')
            ->with([
                'uuid',
                'nome',
                'valor',
                'criado_em',
                'atualizado_em',
            ])
            ->willReturn($servicosEsperados);

        $gateway = new ServicoGateway($repositorio);

        $resultado = $gateway->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
        $this->assertEquals($servicosEsperados, $resultado);
    }

    public function testDeletar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->expects($this->once())
            ->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $gateway = new ServicoGateway($repositorio);

        $resultado = $gateway->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $novosDados = [
            'nome' => 'Novo Nome',
            'valor' => 20000,
        ];

        $retornoEsperado = [
            'uuid' => 'uuid-123',
            'nome' => 'Novo Nome',
            'valor' => 20000,
        ];

        $repositorio->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', $novosDados)
            ->willReturn($retornoEsperado);

        $gateway = new ServicoGateway($repositorio);

        $resultado = $gateway->atualizar('uuid-123', $novosDados);

        $this->assertIsArray($resultado);
        $this->assertEquals($retornoEsperado, $resultado);
    }
}
