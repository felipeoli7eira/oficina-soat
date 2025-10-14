<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\Entity\Cliente\Entidade as Cliente;
use App\Domain\Entity\Veiculo\Entidade as Veiculo;
use App\Domain\UseCase\Ordem\UpdateStatusUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use PHPUnit\Framework\TestCase;
use Mockery;
use DateTimeImmutable;

class UpdateStatusUseCaseTest extends TestCase
{
    private UpdateStatusUseCase $useCase;
    private OrdemGateway $gatewayMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->gatewayMock = Mockery::mock(OrdemGateway::class);
        $this->useCase = new UpdateStatusUseCase($this->gatewayMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testExecAtualizaStatusComSucesso(): void
    {
        $uuid = 'ordem-uuid-123';
        $novoStatus = Ordem::STATUS_EM_EXECUCAO;
        
        $ordemExistente = Mockery::mock(Ordem::class);
        $ordemExistente->status = Ordem::STATUS_APROVADA;

        $dadosAtualizados = [
            'uuid' => $uuid,
            'status' => $novoStatus,
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
            ->with($uuid, $novoStatus)
            ->andReturn($dadosAtualizados);

        $resultado = $this->useCase->exec($uuid, $novoStatus);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertEquals($uuid, $resultado->uuid);
        $this->assertEquals($novoStatus, $resultado->status);
    }

    public function testExecFalhaQuandoUuidVazio(): void
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');
        $this->expectExceptionCode(400);

        $this->useCase->exec('', Ordem::STATUS_APROVADA);
    }

    public function testExecFalhaQuandoOrdemNaoExiste(): void
    {
        $uuid = 'ordem-inexistente';
        $novoStatus = Ordem::STATUS_APROVADA;

        $this->gatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($uuid, 'uuid')
            ->andReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');
        $this->expectExceptionCode(404);

        $this->useCase->exec($uuid, $novoStatus);
    }

    /**
     * @dataProvider statusInvalidos
     */
    public function testExecFalhaQuandoStatusInvalido(string $statusInvalido): void
    {
        $uuid = 'ordem-uuid-123';
        
        $ordemExistente = Mockery::mock(Ordem::class);
        $ordemExistente->status = Ordem::STATUS_RECEBIDA;

        $this->gatewayMock
            ->shouldReceive('encontrarPorIdentificadorUnico')
            ->once()
            ->with($uuid, 'uuid')
            ->andReturn($ordemExistente);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Opções de status disponíveis: ' . implode(', ', [
            Ordem::STATUS_RECEBIDA,
            Ordem::STATUS_EM_DIAGNOSTICO,
            Ordem::STATUS_AGUARDANDO_APROVACAO,
            Ordem::STATUS_APROVADA,
            Ordem::STATUS_REPROVADA,
            Ordem::STATUS_CANCELADA,
            Ordem::STATUS_EM_EXECUCAO,
            Ordem::STATUS_FINALIZADA,
            Ordem::STATUS_ENTREGUE,
        ]));
        $this->expectExceptionCode(400);

        $this->useCase->exec($uuid, $statusInvalido);
    }

    public function testExecChamaGatewayComParametrosCorretos(): void
    {
        $uuid = 'ordem-uuid-123';
        $novoStatus = Ordem::STATUS_CANCELADA;
        
        $ordemExistente = Mockery::mock(Ordem::class);
        $ordemExistente->status = Ordem::STATUS_AGUARDANDO_APROVACAO;

        $dadosAtualizados = [
            'uuid' => $uuid,
            'status' => $novoStatus,
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
            ->with($uuid, $novoStatus)
            ->andReturn($dadosAtualizados);

        $resultado = $this->useCase->exec($uuid, $novoStatus);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertEquals($uuid, $resultado->uuid);
        $this->assertEquals($novoStatus, $resultado->status);
    }

    /**
     * @dataProvider statusValidos
     */
    public function testExecPermiteTodosStatusValidos(string $statusValido): void
    {
        $uuid = 'ordem-uuid-123';
        
        $ordemExistente = Mockery::mock(Ordem::class);
        $ordemExistente->status = Ordem::STATUS_RECEBIDA;

        $dadosAtualizados = [
            'uuid' => $uuid,
            'status' => $statusValido,
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
            ->with($uuid, $statusValido)
            ->andReturn($dadosAtualizados);

        $resultado = $this->useCase->exec($uuid, $statusValido);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertEquals($statusValido, $resultado->status);
    }

    public static function statusInvalidos(): array
    {
        return [
            'status_inexistente' => ['status_inexistente'],
            'vazio' => [''],
            'null' => ['null'],
            'qualquer_coisa' => ['qualquer_coisa'],
        ];
    }

    public static function statusValidos(): array
    {
        return [
            'recebida' => [Ordem::STATUS_RECEBIDA],
            'em_diagnostico' => [Ordem::STATUS_EM_DIAGNOSTICO],
            'aguardando_aprovacao' => [Ordem::STATUS_AGUARDANDO_APROVACAO],
            'aprovada' => [Ordem::STATUS_APROVADA],
            'reprovada' => [Ordem::STATUS_REPROVADA],
            'cancelada' => [Ordem::STATUS_CANCELADA],
            'em_execucao' => [Ordem::STATUS_EM_EXECUCAO],
            'finalizada' => [Ordem::STATUS_FINALIZADA],
            'entregue' => [Ordem::STATUS_ENTREGUE],
        ];
    }
}