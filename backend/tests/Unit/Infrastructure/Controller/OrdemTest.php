<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Controller;

use App\Infrastructure\Controller\Ordem;
use App\Domain\Entity\Ordem\RepositorioInterface as OrdemRepositorio;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;
use App\Domain\Entity\Veiculo\RepositorioInterface as VeiculoRepositorio;
use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepositorio;
use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;
use App\Domain\Entity\Ordem\Entidade as OrdemEntidade;
use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class OrdemTest extends TestCase
{
    public function testUseRepositorio()
    {
        $repositorio = $this->createMock(OrdemRepositorio::class);
        $controller = new Ordem();

        $resultado = $controller->useRepositorio($repositorio);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertSame($repositorio, $resultado->repositorio);
    }

    public function testUseClienteRepositorio()
    {
        $repositorio = $this->createMock(ClienteRepositorio::class);
        $controller = new Ordem();

        $resultado = $controller->useClienteRepositorio($repositorio);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertSame($repositorio, $resultado->clienteRepositorio);
    }

    public function testUseVeiculoRepositorio()
    {
        $repositorio = $this->createMock(VeiculoRepositorio::class);
        $controller = new Ordem();

        $resultado = $controller->useVeiculoRepositorio($repositorio);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertSame($repositorio, $resultado->veiculoRepositorio);
    }

    public function testUseServicoRepositorio()
    {
        $repositorio = $this->createMock(ServicoRepositorio::class);
        $controller = new Ordem();

        $resultado = $controller->useServicoRepositorio($repositorio);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertSame($repositorio, $resultado->servicoRepositorio);
    }

    public function testUseMaterialRepositorio()
    {
        $repositorio = $this->createMock(MaterialRepositorio::class);
        $controller = new Ordem();

        $resultado = $controller->useMaterialRepositorio($repositorio);

        $this->assertInstanceOf(Ordem::class, $resultado);
        $this->assertSame($repositorio, $resultado->materialRepositorio);
    }

    public function testCriar()
    {
        $ordemRepositorio = $this->createMock(OrdemRepositorio::class);
        $clienteRepositorio = $this->createMock(ClienteRepositorio::class);
        $veiculoRepositorio = $this->createMock(VeiculoRepositorio::class);

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

        $ordemEntidade = new OrdemEntidade(
            uuid: 'ordem-uuid',
            cliente: $clienteEntidade,
            veiculo: $veiculoEntidade,
            dtAbertura: new DateTimeImmutable(),
            status: 'RECEBIDA',
            servicos: [],
            materiais: []
        );

        $clienteRepositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($clienteEntidade);

        $veiculoRepositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($veiculoEntidade);

        $ordemRepositorio->method('criar')
            ->willReturn([
                'uuid' => 'ordem-uuid',
                'cliente_uuid' => 'cliente-uuid',
                'veiculo_uuid' => 'veiculo-uuid',
                'status' => 'RECEBIDA',
                'dt_abertura' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'descricao' => 'Descrição teste'
            ]);

        $ordemRepositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($ordemEntidade);

        $controller = new Ordem();
        $controller->useRepositorio($ordemRepositorio)
            ->useClienteRepositorio($clienteRepositorio)
            ->useVeiculoRepositorio($veiculoRepositorio);

        $resultado = $controller->criar('cliente-uuid', 'veiculo-uuid', 'Descrição teste');

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('uuid', $resultado);
    }

    public function testListar()
    {
        $ordemRepositorio = $this->createMock(OrdemRepositorio::class);

        $ordemRepositorio->method('listar')
            ->willReturn([
                [
                    'uuid' => 'ordem-1',
                    'status' => 'RECEBIDA',
                    'dt_abertura' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'descricao' => 'Descrição da ordem 1',
                    'dt_finalizacao' => null,
                    'dt_atualizacao' => null,
                    'servicos' => [],
                    'materiais' => [],
                    'cliente' => [
                        'uuid' => 'cliente-1',
                        'nome' => 'Cliente Teste 1',
                        'documento' => '12345678901',
                        'email' => 'cliente1@test.com',
                        'fone' => '11999999999',
                        'criado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                        'atualizado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s')
                    ],
                    'veiculo' => [
                        'uuid' => 'veiculo-1',
                        'cliente_id' => 1,
                        'placa' => 'ABC1234',
                        'marca' => 'Toyota',
                        'modelo' => 'Corolla',
                        'ano' => 2022,
                        'criado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                        'atualizado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s')
                    ]
                ],
                [
                    'uuid' => 'ordem-2',
                    'status' => 'EM_DIAGNOSTICO',
                    'dt_abertura' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'descricao' => 'Descrição da ordem 2',
                    'dt_finalizacao' => null,
                    'dt_atualizacao' => null,
                    'servicos' => [],
                    'materiais' => [],
                    'cliente' => [
                        'uuid' => 'cliente-2',
                        'nome' => 'Cliente Teste 2',
                        'documento' => '12345678902',
                        'email' => 'cliente2@test.com',
                        'fone' => '11999999998',
                        'criado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                        'atualizado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s')
                    ],
                    'veiculo' => [
                        'uuid' => 'veiculo-2',
                        'cliente_id' => 2,
                        'placa' => 'DEF5678',
                        'marca' => 'Honda',
                        'modelo' => 'Civic',
                        'ano' => 2021,
                        'criado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                        'atualizado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s')
                    ]
                ],
            ]);

        $controller = new Ordem();
        $controller->useRepositorio($ordemRepositorio);

        $resultado = $controller->listar();

        $this->assertIsArray($resultado);
    }

    public function testObterUm()
    {
        $ordemRepositorio = $this->createMock(OrdemRepositorio::class);

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

        $ordemEntidade = new OrdemEntidade(
            uuid: 'ordem-uuid',
            cliente: $clienteEntidade,
            veiculo: $veiculoEntidade,
            dtAbertura: new DateTimeImmutable(),
            status: 'RECEBIDA',
            servicos: [],
            materiais: []
        );

        $ordemRepositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($ordemEntidade);

        $controller = new Ordem();
        $controller->useRepositorio($ordemRepositorio);

        $resultado = $controller->obterUm('ordem-uuid');

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('uuid', $resultado);
    }

    public function testObterUmRetornaNull()
    {
        $ordemRepositorio = $this->createMock(OrdemRepositorio::class);

        $ordemRepositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $controller = new Ordem();
        $controller->useRepositorio($ordemRepositorio);

        $resultado = $controller->obterUm('uuid-inexistente');

        $this->assertNull($resultado);
    }

    public function testDeletar()
    {
        $ordemRepositorio = $this->createMock(OrdemRepositorio::class);

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

        $ordemEntidade = new OrdemEntidade(
            uuid: 'ordem-uuid',
            cliente: $clienteEntidade,
            veiculo: $veiculoEntidade,
            dtAbertura: new DateTimeImmutable(),
            status: 'RECEBIDA',
            servicos: [],
            materiais: []
        );

        $ordemRepositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($ordemEntidade);

        $ordemRepositorio->method('deletar')
            ->with('ordem-uuid')
            ->willReturn(true);

        $controller = new Ordem();
        $controller->useRepositorio($ordemRepositorio);

        $resultado = $controller->deletar('ordem-uuid');

        $this->assertTrue($resultado);
    }

    public function testAtualizar()
    {
        $ordemRepositorio = $this->createMock(OrdemRepositorio::class);

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

        $ordemEntidade = new OrdemEntidade(
            uuid: 'ordem-uuid',
            cliente: $clienteEntidade,
            veiculo: $veiculoEntidade,
            dtAbertura: new DateTimeImmutable(),
            status: 'EM_EXECUCAO',
            servicos: [],
            materiais: []
        );

        $ordemRepositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($ordemEntidade);

        $ordemRepositorio->method('atualizar')
            ->willReturn([
                'uuid' => 'ordem-uuid',
                'status' => 'EM_EXECUCAO',
                'dt_abertura' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'descricao' => 'Descrição atualizada',
                'dt_finalizacao' => null,
                'dt_atualizacao' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'servicos' => [],
                'materiais' => [],
                'cliente' => [
                    'uuid' => 'cliente-uuid',
                    'nome' => 'Cliente Teste',
                    'documento' => '12345678901',
                    'email' => 'cliente@test.com',
                    'fone' => '11999999999',
                    'criado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'atualizado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'deletado_em' => null
                ],
                'veiculo' => [
                    'uuid' => 'veiculo-uuid',
                    'cliente_id' => 1,
                    'placa' => 'ABC1234',
                    'marca' => 'Toyota',
                    'modelo' => 'Corolla',
                    'ano' => 2022,
                    'criado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'atualizado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                    'deletado_em' => null
                ]
            ]);

        $controller = new Ordem();
        $controller->useRepositorio($ordemRepositorio);

        $resultado = $controller->atualizar('ordem-uuid', ['status' => 'EM_EXECUCAO']);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('uuid', $resultado);
    }
}
