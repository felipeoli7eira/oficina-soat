<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Infrastructure\Gateway\OrdemGateway;
use App\Domain\Entity\Ordem\RepositorioInterface;
use App\Domain\Entity\Ordem\Entidade;
use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class OrdemGatewayTest extends TestCase
{
    public function testEncontrarPorIdentificadorUnico()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $clienteEntidade = new ClienteEntidade(
            uuid: 'cliente-uuid',
            nome: 'Cliente Teste',
            documento: '12345678901',
            email: 'cliente@test.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $veiculoEntidade = new VeiculoEntidade(
            uuid: 'veiculo-uuid',
            clienteId: 1,
            placa: 'ABC1234',
            marca: 'Toyota',
            modelo: 'Corolla',
            ano: 2022,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade = new Entidade(
            uuid: 'uuid-123',
            cliente: $clienteEntidade,
            veiculo: $veiculoEntidade,
            dtAbertura: new DateTimeImmutable(),
            status: 'RECEBIDA'
        );

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidade);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->encontrarPorIdentificadorUnico('uuid-123', 'uuid');

        $this->assertInstanceOf(Entidade::class, $result);
    }

    public function testEncontrarPorIdentificadorUnicoRetornaNullQuandoNaoEncontrado()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->encontrarPorIdentificadorUnico('uuid-inexistente', 'uuid');

        $this->assertNull($result);
    }

    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('criar')
            ->willReturn([
                'uuid' => 'uuid-gerado',
                'cliente_uuid' => 'cliente-uuid',
                'veiculo_uuid' => 'veiculo-uuid',
                'status' => 'RECEBIDA',
            ]);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->criar('cliente-uuid', 'veiculo-uuid', [
            'status' => 'RECEBIDA',
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('uuid', $result);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('listar')
            ->willReturn([
                ['uuid' => 'ordem-1', 'status' => 'RECEBIDA'],
                ['uuid' => 'ordem-2', 'status' => 'EM_DIAGNOSTICO'],
            ]);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->listar();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testDeletar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->deletar('uuid-123');

        $this->assertTrue($result);
    }

    public function testDeletarRetornaFalseQuandoNaoEncontra()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('deletar')
            ->willReturn(false);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->deletar('uuid-inexistente');

        $this->assertFalse($result);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'status' => 'EM_EXECUCAO',
            ]);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->atualizar('uuid-123', ['status' => 'EM_EXECUCAO']);

        $this->assertIsArray($result);
        $this->assertEquals('EM_EXECUCAO', $result['status']);
    }

    public function testObterIdNumerico()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('obterIdNumerico')
            ->with('uuid-123')
            ->willReturn(42);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->obterIdNumerico('uuid-123');

        $this->assertEquals(42, $result);
    }

    public function testObterIdNumericoRetornaMenosUmQuandoNaoEncontra()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('obterIdNumerico')
            ->willReturn(-1);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->obterIdNumerico('uuid-inexistente');

        $this->assertEquals(-1, $result);
    }

    public function testObterOrdensDoClienteComStatusDiferenteDe()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('obterOrdensDoClienteComStatusDiferenteDe')
            ->willReturn([
                ['uuid' => 'ordem-1', 'status' => 'RECEBIDA'],
            ]);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->obterOrdensDoClienteComStatusDiferenteDe('cliente-uuid', 'FINALIZADA');

        $this->assertIsArray($result);
    }

    public function testAtualizarStatus()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('atualizarStatus')
            ->willReturn([
                'uuid' => 'uuid-123',
                'status' => 'APROVADA',
            ]);

        $gateway = new OrdemGateway($repositorio);
        $result = $gateway->atualizarStatus('uuid-123', 'APROVADA');

        $this->assertIsArray($result);
        $this->assertEquals('APROVADA', $result['status']);
    }

    public function testConstrutorInicializaRepositorio()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $gateway = new OrdemGateway($repositorio);

        $this->assertSame($repositorio, $gateway->repositorio);
    }
}
