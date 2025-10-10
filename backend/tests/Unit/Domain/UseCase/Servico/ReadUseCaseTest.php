<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\UseCase\Servico\ReadUseCase;
use App\Infrastructure\Gateway\ServicoGateway;
use PHPUnit\Framework\TestCase;

class ReadUseCaseTest extends TestCase
{
    public function testExecRetornaListaDeServicos()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $servicosEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'Troca de óleo',
                'valor' => 15000,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Alinhamento',
                'valor' => 12000,
                'criado_em' => '2025-01-02 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
            ],
        ];

        $gateway->expects($this->once())
            ->method('listar')
            ->willReturn($servicosEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    public function testExecRetornaListaVazia()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $gateway->expects($this->once())
            ->method('listar')
            ->willReturn([]);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }

    public function testExecFormataValoresCorretamente()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $servicosEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'Troca de óleo',
                'valor' => 15050,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-01 10:00:00',
            ],
        ];

        $gateway->method('listar')
            ->willReturn($servicosEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertEquals(150.5, $resultado[0]['valor']);
    }
}
