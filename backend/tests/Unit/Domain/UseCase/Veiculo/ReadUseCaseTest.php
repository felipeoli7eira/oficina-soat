<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Veiculo;

use App\Domain\UseCase\Veiculo\ReadUseCase;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;
use PHPUnit\Framework\TestCase;

class ReadUseCaseTest extends TestCase
{
    public function testExecRetornaListaDeVeiculos()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

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
            [
                'uuid' => 'uuid-2',
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'placa' => 'XYZ5678',
                'ano' => 2024,
                'cliente_id' => 2,
                'criado_em' => '2025-01-02 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
            ],
        ];

        $veiculoGateway->expects($this->once())
            ->method('listar')
            ->willReturn($veiculosEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    public function testExecRetornaListaVazia()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

        $veiculoGateway->expects($this->once())
            ->method('listar')
            ->willReturn([]);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }

    public function testExecFormataVeiculosCorretamente()
    {
        $veiculoGateway = $this->createMock(VeiculoGateway::class);
        $clienteGateway = $this->createMock(ClienteGateway::class);

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

        $veiculoGateway->method('listar')
            ->willReturn($veiculosEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($veiculoGateway, $clienteGateway);

        $this->assertArrayHasKey('uuid', $resultado[0]);
        $this->assertArrayHasKey('marca', $resultado[0]);
        $this->assertArrayHasKey('modelo', $resultado[0]);
        $this->assertArrayHasKey('placa', $resultado[0]);
        $this->assertArrayHasKey('ano', $resultado[0]);
    }
}
