<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\UseCase\Ordem\RemoveServiceUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use App\Infrastructure\Gateway\ServicoGateway;
use PHPUnit\Framework\TestCase;
use Mockery;

class RemoveServiceUseCaseTest extends TestCase
{
    private RemoveServiceUseCase $useCase;
    private OrdemGateway $ordemGatewayMock;
    private ServicoGateway $servicoGatewayMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ordemGatewayMock = Mockery::mock(OrdemGateway::class);
        $this->servicoGatewayMock = Mockery::mock(ServicoGateway::class);
        
        $this->useCase = new RemoveServiceUseCase(
            $this->ordemGatewayMock,
            $this->servicoGatewayMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testExecRemoveServicoComSucesso(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $servicoUuid = 'servico-uuid-456';
        $resultadoRemocao = 1;

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_RECEBIDA;

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->ordemGatewayMock
            ->shouldReceive('removerServico')
            ->once()
            ->with($ordemUuid, $servicoUuid)
            ->andReturn($resultadoRemocao);

        $resultado = $this->useCase->exec($ordemUuid, $servicoUuid);

        $this->assertEquals($resultadoRemocao, $resultado);
    }

    public function testExecRemoveServicoComOrdemAguardandoAprovacao(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $servicoUuid = 'servico-uuid-456';
        $resultadoRemocao = 1;

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_AGUARDANDO_APROVACAO;

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->ordemGatewayMock
            ->shouldReceive('removerServico')
            ->once()
            ->with($ordemUuid, $servicoUuid)
            ->andReturn($resultadoRemocao);

        $resultado = $this->useCase->exec($ordemUuid, $servicoUuid);

        $this->assertEquals($resultadoRemocao, $resultado);
    }

    public function testExecFalhaQuandoOrdemNaoExiste(): void
    {
        $ordemUuid = 'ordem-inexistente';
        $servicoUuid = 'servico-uuid-456';

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Ordem não existe');
        $this->expectExceptionCode(404);

        $this->useCase->exec($ordemUuid, $servicoUuid);
    }

    /**
     * @dataProvider statusQueNaoPermitemRemocaoServico
     */
    public function testExecFalhaQuandoOrdemNaoPermiteRemocaoServico(string $status): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $servicoUuid = 'servico-uuid-456';

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = $status;

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Apenas ordens que estão aguardando aprovação podem ter serviços removidos');
        $this->expectExceptionCode(400);

        $this->useCase->exec($ordemUuid, $servicoUuid);
    }

    public static function statusQueNaoPermitemRemocaoServico(): array
    {
        return [
            'em_diagnostico' => [Ordem::STATUS_EM_DIAGNOSTICO],
            'aprovada' => [Ordem::STATUS_APROVADA],
            'reprovada' => [Ordem::STATUS_REPROVADA],
            'cancelada' => [Ordem::STATUS_CANCELADA],
            'em_execucao' => [Ordem::STATUS_EM_EXECUCAO],
            'finalizada' => [Ordem::STATUS_FINALIZADA],
            'entregue' => [Ordem::STATUS_ENTREGUE],
        ];
    }
}