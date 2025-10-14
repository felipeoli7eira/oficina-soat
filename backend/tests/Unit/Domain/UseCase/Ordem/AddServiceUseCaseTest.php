<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\Entity\Servico\Entidade as Servico;
use App\Domain\UseCase\Ordem\AddServiceUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use App\Infrastructure\Gateway\ServicoGateway;
use PHPUnit\Framework\TestCase;
use Mockery;
use DateTimeImmutable;

class AddServiceUseCaseTest extends TestCase
{
    private AddServiceUseCase $useCase;
    private OrdemGateway $ordemGatewayMock;
    private ServicoGateway $servicoGatewayMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ordemGatewayMock = Mockery::mock(OrdemGateway::class);
        $this->servicoGatewayMock = Mockery::mock(ServicoGateway::class);
        
        $this->useCase = new AddServiceUseCase(
            $this->ordemGatewayMock,
            $this->servicoGatewayMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testExecAdicionaServicoComSucesso(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $servicoUuid = 'servico-uuid-456';
        $resultadoUuid = 'servico-ordem-uuid-789';

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_RECEBIDA;

        $servico = Mockery::mock(Servico::class);

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->servicoGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($servicoUuid, 'uuid')
            ->andReturn($servico);

        $this->ordemGatewayMock
            ->shouldReceive('adicionarServico')
            ->once()
            ->with($ordemUuid, $servicoUuid)
            ->andReturn($resultadoUuid);

        $resultado = $this->useCase->exec($ordemUuid, $servicoUuid);

        $this->assertEquals($resultadoUuid, $resultado);
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

    public function testExecFalhaQuandoServicoNaoExiste(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $servicoUuid = 'servico-inexistente';

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_RECEBIDA;

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->servicoGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($servicoUuid, 'uuid')
            ->andReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Serviço não encontrado na base');
        $this->expectExceptionCode(404);

        $this->useCase->exec($ordemUuid, $servicoUuid);
    }

    /**
     * @dataProvider statusQueNaoPermitemAdicaoServico
     */
    public function testExecFalhaQuandoOrdemNaoPermiteAdicaoServico(string $status): void
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
        $this->expectExceptionMessage('Essa ordem não pode mais receber serviços');
        $this->expectExceptionCode(400);

        $this->useCase->exec($ordemUuid, $servicoUuid);
    }

    public function testExecChamaGatewayComParametrosCorretos(): void
    {
        $ordemUuid = 'ordem-uuid-123';
        $servicoUuid = 'servico-uuid-456';
        $resultadoUuid = 'servico-ordem-uuid-789';

        $ordem = Mockery::mock(Ordem::class);
        $ordem->status = Ordem::STATUS_AGUARDANDO_APROVACAO;

        $servico = Mockery::mock(Servico::class);

        $this->ordemGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($ordemUuid, 'uuid')
            ->andReturn($ordem);

        $this->servicoGatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($servicoUuid, 'uuid')
            ->andReturn($servico);

        $this->ordemGatewayMock
            ->shouldReceive('adicionarServico')
            ->once()
            ->with($ordemUuid, $servicoUuid)
            ->andReturn($resultadoUuid);

        $resultado = $this->useCase->exec($ordemUuid, $servicoUuid);

        $this->assertEquals($resultadoUuid, $resultado);
    }

    public static function statusQueNaoPermitemAdicaoServico(): array
    {
        return [
            'finalizada' => [Ordem::STATUS_FINALIZADA],
            'cancelada' => [Ordem::STATUS_CANCELADA],
            'reprovada' => [Ordem::STATUS_REPROVADA],
            'entregue' => [Ordem::STATUS_ENTREGUE],
        ];
    }
}