<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\UseCase\Ordem\ReadUseCase;
use App\Infrastructure\Gateway\OrdemGateway;
use PHPUnit\Framework\TestCase;

class ReadUseCaseTest extends TestCase
{

    public function testExecRetornaListaDeOrdens()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $ordensEsperadas = [
            [
                'uuid' => 'ordem-uuid-1',
                'cliente' => [
                    'uuid' => 'cliente-uuid-1',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-1',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                ],
                'descricao' => 'Manutenção preventiva',
                'status' => 'RECEBIDA',
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => null,
                'servicos' => [],
                'materiais' => [],
            ],
            [
                'uuid' => 'ordem-uuid-2',
                'cliente' => [
                    'uuid' => 'cliente-uuid-2',
                    'nome' => 'Maria Santos',
                    'documento' => '98765432109',
                    'email' => 'maria@example.com',
                    'fone' => '11988888888',
                    'criado_em' => '2025-01-02 10:00:00',
                    'atualizado_em' => '2025-01-02 10:00:00',
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-2',
                    'marca' => 'Honda',
                    'modelo' => 'Civic',
                    'placa' => 'XYZ5678',
                    'ano' => 2021,
                    'cliente_id' => 2,
                    'criado_em' => '2025-01-02 10:00:00',
                    'atualizado_em' => '2025-01-02 10:00:00',
                ],
                'descricao' => 'Troca de óleo',
                'status' => 'EM_EXECUCAO',
                'dt_abertura' => '2025-01-02 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => '2025-01-02 11:00:00',
                'servicos' => [],
                'materiais' => [],
            ],
        ];

        $gateway->expects($this->once())
            ->method('listar')
            ->with([])
            ->willReturn($ordensEsperadas);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    public function testExecRetornaListaVazia()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $gateway->expects($this->once())
            ->method('listar')
            ->with([])
            ->willReturn([]);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }

    public function testExecFormataOrdensCorretamente()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $ordensEsperadas = [
            [
                'uuid' => 'ordem-uuid-1',
                'cliente' => [
                    'uuid' => 'cliente-uuid-1',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-1',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                ],
                'descricao' => 'Manutenção preventiva',
                'status' => 'RECEBIDA',
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => null,
                'servicos' => [],
                'materiais' => [],
            ],
        ];

        $gateway->method('listar')
            ->willReturn($ordensEsperadas);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertArrayHasKey('uuid', $resultado[0]);
        $this->assertArrayHasKey('cliente', $resultado[0]);
        $this->assertArrayHasKey('veiculo', $resultado[0]);
        $this->assertArrayHasKey('descricao', $resultado[0]);
        $this->assertArrayHasKey('status', $resultado[0]);
        $this->assertArrayHasKey('servicos', $resultado[0]);
        $this->assertArrayHasKey('materiais', $resultado[0]);
        $this->assertArrayHasKey('dt_abertura', $resultado[0]);
    }

    public function testExecComFiltros()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $filtros = ['status' => 'RECEBIDA'];

        $ordensEsperadas = [
            [
                'uuid' => 'ordem-uuid-1',
                'cliente' => [
                    'uuid' => 'cliente-uuid-1',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-1',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                ],
                'descricao' => 'Manutenção preventiva',
                'status' => 'RECEBIDA',
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => null,
                'servicos' => [],
                'materiais' => [],
            ],
        ];

        $gateway->expects($this->once())
            ->method('listar')
            ->with($filtros)
            ->willReturn($ordensEsperadas);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway, $filtros);

        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertEquals('RECEBIDA', $resultado[0]['status']);
    }

    public function testExecComServicosEMateriais()
    {
        $gateway = $this->createMock(OrdemGateway::class);

        $ordensEsperadas = [
            [
                'uuid' => 'ordem-uuid-1',
                'cliente' => [
                    'uuid' => 'cliente-uuid-1',
                    'nome' => 'João Silva',
                    'documento' => '12345678901',
                    'email' => 'joao@example.com',
                    'fone' => '11999999999',
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid-1',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'placa' => 'ABC1234',
                    'ano' => 2020,
                    'cliente_id' => 1,
                    'criado_em' => '2025-01-01 10:00:00',
                    'atualizado_em' => '2025-01-01 10:00:00',
                ],
                'descricao' => 'Manutenção completa',
                'status' => 'EM_EXECUCAO',
                'dt_abertura' => '2025-01-01 10:00:00',
                'dt_finalizacao' => null,
                'dt_atualizacao' => null,
                'servicos' => [
                    [
                        'uuid' => 'servico-uuid-1',
                        'nome' => 'Troca de óleo',
                        'valor' => 15000,
                    ]
                ],
                'materiais' => [
                    [
                        'uuid' => 'material-uuid-1',
                        'nome' => 'Óleo 5W30',
                        'preco_uso_interno' => 10000,
                    ]
                ],
            ],
        ];

        $gateway->method('listar')
            ->willReturn($ordensEsperadas);

        $useCase = new ReadUseCase();

        $resultado = $useCase->exec($gateway);

        $this->assertIsArray($resultado[0]['servicos']);
        $this->assertIsArray($resultado[0]['materiais']);
        $this->assertArrayHasKey('total_servicos', $resultado[0]);
        $this->assertArrayHasKey('total_materiais', $resultado[0]);
        $this->assertArrayHasKey('total_geral', $resultado[0]);
    }
}
