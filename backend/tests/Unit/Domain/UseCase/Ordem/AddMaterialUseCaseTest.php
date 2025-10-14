<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\Entity\Material\Entidade as Material;
use App\Domain\UseCase\Ordem\AddMaterialUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use App\Infrastructure\Gateway\MaterialGateway;
use PHPUnit\Framework\TestCase;
use Mockery;
use DateTimeImmutable;

class AddMaterialUseCaseTest extends TestCase
{
    private AddMaterialUseCase $useCase;
    private OrdemGateway $ordemGatewayMock;
    private MaterialGateway $materialGatewayMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ordemGatewayMock = Mockery::mock(OrdemGateway::class);
        $this->materialGatewayMock = Mockery::mock(MaterialGateway::class);
        
        $this->useCase = new AddMaterialUseCase(
            $this->ordemGatewayMock,
            $this->materialGatewayMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testExecAdicionaMaterialComSucesso(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $materialUuid = 'material-uuid-456';
        $resultadoUuid = 'material-ordem-uuid-789';

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_RECEBIDA;

        $material = Mockery::mock(Material::class);

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->materialGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($materialUuid, 'uuid')
            ->andReturn($material);

        $this->ordemGatewayMock
            ->shouldReceive('adicionarMaterial')
            ->once()
            ->with($ordemUuid, $materialUuid)
            ->andReturn($resultadoUuid);

        $resultado = $this->useCase->exec($ordemUuid, $materialUuid);

        $this->assertEquals($resultadoUuid, $resultado);
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

    public function testExecFalhaQuandoMaterialNaoExiste(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $materialUuid = 'material-inexistente';

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_RECEBIDA;

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->materialGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($materialUuid, 'uuid')
            ->andReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Material não encontrado');
        $this->expectExceptionCode(404);

        $this->useCase->exec($ordemUuid, $materialUuid);
    }

    /**
     * @dataProvider statusQueNaoPermitemAdicaoMaterial
     */
    public function testExecFalhaQuandoOrdemNaoPermiteAdicaoMaterial(string $status): void
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
        $this->expectExceptionMessage('Essa ordem não pode mais receber materiais');
        $this->expectExceptionCode(400);

        $this->useCase->exec($ordemUuid, $materialUuid);
    }

    public function testExecChamaGatewayComParametrosCorretos(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $materialUuid = 'material-uuid-456';
        $resultadoUuid = 'material-ordem-uuid-789';

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_RECEBIDA;

        $material = Mockery::mock(Material::class);

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->materialGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($materialUuid, 'uuid')
            ->andReturn($material);

        $this->ordemGatewayMock
            ->shouldReceive('adicionarMaterial')
            ->once()
            ->with($ordemUuid, $materialUuid)
            ->andReturn($resultadoUuid);

        $resultado = $this->useCase->exec($ordemUuid, $materialUuid);

        $this->assertEquals($resultadoUuid, $resultado);
    }

    public static function statusQueNaoPermitemAdicaoMaterial(): array
    {
        return [
            'finalizada' => [Ordem::STATUS_FINALIZADA],
            'cancelada' => [Ordem::STATUS_CANCELADA],
            'reprovada' => [Ordem::STATUS_REPROVADA],
            'entregue' => [Ordem::STATUS_ENTREGUE],
        ];
    }
}