<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\UseCase\Ordem\RemoveMaterialUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use App\Infrastructure\Gateway\MaterialGateway;
use PHPUnit\Framework\TestCase;
use Mockery;

class RemoveMaterialUseCaseTest extends TestCase
{
    private RemoveMaterialUseCase $useCase;
    private OrdemGateway $ordemGatewayMock;
    private MaterialGateway $materialGatewayMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ordemGatewayMock = Mockery::mock(OrdemGateway::class);
        $this->materialGatewayMock = Mockery::mock(MaterialGateway::class);
        
        $this->useCase = new RemoveMaterialUseCase(
            $this->ordemGatewayMock,
            $this->materialGatewayMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testExecRemoveMaterialComSucesso(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $materialUuid = 'material-uuid-456';
        $resultadoRemocao = 1;

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_RECEBIDA;

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->ordemGatewayMock
            ->shouldReceive('removerMaterial')
            ->once()
            ->with($ordemUuid, $materialUuid)
            ->andReturn($resultadoRemocao);

        $resultado = $this->useCase->exec($ordemUuid, $materialUuid);

        $this->assertEquals($resultadoRemocao, $resultado);
    }

    public function testExecRemoveMaterialComOrdemAguardandoAprovacao(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $materialUuid = 'material-uuid-456';
        $resultadoRemocao = 1;

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_AGUARDANDO_APROVACAO;

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->ordemGatewayMock
            ->shouldReceive('removerMaterial')
            ->once()
            ->with($ordemUuid, $materialUuid)
            ->andReturn($resultadoRemocao);

        $resultado = $this->useCase->exec($ordemUuid, $materialUuid);

        $this->assertEquals($resultadoRemocao, $resultado);
    }

    public function testExecFalhaQuandoOrdemNaoExiste(): void
    {
        $ordemUuid = 'ordem-inexistente';
        $materialUuid = 'material-uuid-456';

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Ordem não existe');
        $this->expectExceptionCode(404);

        $this->useCase->exec($ordemUuid, $materialUuid);
    }

    /**
     * @dataProvider statusQueNaoPermitemRemocaoMaterial
     */
    public function testExecFalhaQuandoOrdemNaoPermiteRemocaoMaterial(string $status): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $materialUuid = 'material-uuid-456';

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = $status;

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Apenas ordens recebidas ou que estão aguardando aprovação podem ter material removido');
        $this->expectExceptionCode(400);

        $this->useCase->exec($ordemUuid, $materialUuid);
    }

    public static function statusQueNaoPermitemRemocaoMaterial(): array
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