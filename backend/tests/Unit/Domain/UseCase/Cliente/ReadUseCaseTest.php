<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Cliente;

use App\Domain\UseCase\Cliente\ReadUseCase;
use App\Infrastructure\Gateway\ClienteGateway;
use PHPUnit\Framework\TestCase;

class ReadUseCaseTest extends TestCase
{
    public function testExecRetornaListaDeClientes()
    {
        $gateway = $this->createMock(ClienteGateway::class);

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

        $gateway->expects($this->once())
            ->method('listar')
            ->willReturn($clientesEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    public function testExecRetornaListaVazia()
    {
        $gateway = $this->createMock(ClienteGateway::class);

        $gateway->expects($this->once())
            ->method('listar')
            ->willReturn([]);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }

    public function testExecFormataClientesCorretamente()
    {
        $gateway = $this->createMock(ClienteGateway::class);

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
        ];

        $gateway->method('listar')
            ->willReturn($clientesEsperados);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertArrayHasKey('uuid', $resultado[0]);
        $this->assertArrayHasKey('nome', $resultado[0]);
        $this->assertArrayHasKey('documento', $resultado[0]);
        $this->assertArrayHasKey('email', $resultado[0]);
        $this->assertArrayHasKey('fone', $resultado[0]);
    }
}
