<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\Entity\Cliente\Entidade as Cliente;
use App\Domain\Entity\Veiculo\Entidade as Veiculo;
use App\Domain\UseCase\Ordem\ApproveUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use PHPUnit\Framework\TestCase;
use Mockery;
use DateTimeImmutable;

class ApproveUseCaseTest extends TestCase
{
    private ApproveUseCase $useCase;
    private OrdemGateway $gatewayMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->gatewayMock = Mockery::mock(OrdemGateway::class);
        $this->useCase = new ApproveUseCase($this->gatewayMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testExecAprovaOrdemComSucesso(): void
    {
        $uuid = 'ordem-uuid-123';
        
        $ordemExistente = Mockery::mock(Ordem::class);
        $ordemExistente->status = Ordem::STATUS_AGUARDANDO_APROVACAO;

        $dadosAtualizados = [
            'uuid' => $uuid,
            'status' => Ordem::STATUS_APROVADA,
            'descricao' => 'Teste',
            'dt_abertura' => '2023-01-01 10:00:00',
            'dt_finalizacao' => null,
            'dt_atualizacao' => '2023-01-01 11:00:00',
            'servicos' => [],
            'materiais' => [],
            'cliente' => [
                'uuid' => 'cliente-uuid',
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'joao@email.com',
                'fone' => '11999999999',
                'criado_em' => '2023-01-01 10:00:00',
                'atualizado_em' => '2023-01-01 10:00:00',
                'deletado_em' => null,
            ],
            'veiculo' => [
                'uuid' => 'veiculo-uuid',
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'placa' => 'ABC-1234',
                'ano' => 2020,
                'cliente_id' => 1,
                'criado_em' => '2023-01-01 10:00:00',
                'atualizado_em' => '2023-01-01 10:00:00',
                'deletado_em' => null,
            ]
        ];

        $this->gatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($uuid, 'uuid')
            ->andReturn($ordemExistente);

        $this->gatewayMock
            ->shouldReceive('atualizarStatus')
            ->once()
            ->with($uuid, Ordem::STATUS_APROVADA)
            ->andReturn($dadosAtualizados);

        $resultado = $this->useCase->exec($uuid);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertEquals($uuid, $resultado->uuid);
        $this->assertEquals(Ordem::STATUS_APROVADA, $resultado->status);
    }

    public function testExecFalhaQuandoUuidVazio(): void
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Ordem não informada corretamente');
        $this->expectExceptionCode(400);

        $this->useCase->exec('');
    }

    public function testExecFalhaQuandoOrdemNaoExiste(): void
    {
        $uuid = 'ordem-inexistente';

        $this->gatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($uuid, 'uuid')
            ->andReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');
        $this->expectExceptionCode(404);

        $this->useCase->exec($uuid);
    }

    public function testExecFalhaQuandoOrdemJaEstaAprovada(): void
    {
        $uuid = 'ordem-uuid-123';
        
        $ordemExistente = Mockery::mock(Ordem::class);
        $ordemExistente->status = Ordem::STATUS_APROVADA;

        $this->gatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($uuid, 'uuid')
            ->andReturn($ordemExistente);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Ordem já está aprovada');
        $this->expectExceptionCode(400);

        $this->useCase->exec($uuid);
    }

    /**
     * @dataProvider statusQueNaoPermitemAprovacao
     */
    public function testExecFalhaQuandoStatusNaoPermiteAprovacao(string $status): void
    {
        $uuid = 'ordem-uuid-123';
        
        $ordemExistente = Mockery::mock(Ordem::class);
        $ordemExistente->status = $status;

        $this->gatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($uuid, 'uuid')
            ->andReturn($ordemExistente);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Ordem não pode mais ser aprovada pois seu status atual é: ' . $status);
        $this->expectExceptionCode(404);

        $this->useCase->exec($uuid);
    }

    public function testExecChamaGatewayComParametrosCorretos(): void
    {
        $uuid = 'ordem-uuid-123';
        
        $ordemExistente = Mockery::mock(Ordem::class);
        $ordemExistente->status = Ordem::STATUS_AGUARDANDO_APROVACAO;

        $dadosAtualizados = [
            'uuid' => $uuid,
            'status' => Ordem::STATUS_APROVADA,
            'descricao' => 'Teste validação',
            'dt_abertura' => '2023-01-01 10:00:00',
            'dt_finalizacao' => null,
            'dt_atualizacao' => '2023-01-01 11:00:00',
            'servicos' => [],
            'materiais' => [],
            'cliente' => [
                'uuid' => 'cliente-uuid',
                'nome' => 'João Silva',
                'documento' => '12345678901',
                'email' => 'joao@email.com',
                'fone' => '11999999999',
                'criado_em' => '2023-01-01 10:00:00',
                'atualizado_em' => '2023-01-01 10:00:00',
                'deletado_em' => null,
            ],
            'veiculo' => [
                'uuid' => 'veiculo-uuid',
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'placa' => 'ABC-1234',
                'ano' => 2020,
                'cliente_id' => 1,
                'criado_em' => '2023-01-01 10:00:00',
                'atualizado_em' => '2023-01-01 10:00:00',
                'deletado_em' => null,
            ]
        ];

        $this->gatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($uuid, 'uuid')
            ->andReturn($ordemExistente);

        $this->gatewayMock
            ->shouldReceive('atualizarStatus')
            ->once()
            ->with($uuid, Ordem::STATUS_APROVADA)
            ->andReturn($dadosAtualizados);

        $resultado = $this->useCase->exec($uuid);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertEquals($uuid, $resultado->uuid);
        $this->assertEquals(Ordem::STATUS_APROVADA, $resultado->status);
    }

    public static function statusQueNaoPermitemAprovacao(): array
    {
        return [
            'reprovada' => [Ordem::STATUS_REPROVADA],
            'cancelada' => [Ordem::STATUS_CANCELADA],
            'em_execucao' => [Ordem::STATUS_EM_EXECUCAO],
            'finalizada' => [Ordem::STATUS_FINALIZADA],
            'entregue' => [Ordem::STATUS_ENTREGUE],
        ];
    }
}