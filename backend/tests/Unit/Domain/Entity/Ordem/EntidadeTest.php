<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Ordem;

use App\Domain\Entity\Cliente\Entidade as Cliente;
use App\Domain\Entity\Ordem\Entidade;
use App\Domain\Entity\Veiculo\Entidade as Veiculo;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    private function criarClienteMock(): Cliente
    {
        $clienteMock = $this->createMock(Cliente::class);
        $clienteMock->method('toExternal')->willReturn([
            'uuid' => 'cliente-uuid-123',
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
            'criado_em' => '01/01/2025 10:00',
            'atualizado_em' => '01/01/2025 10:00',
        ]);

        return $clienteMock;
    }

    private function criarVeiculoMock(): Veiculo
    {
        $veiculoMock = $this->createMock(Veiculo::class);
        $veiculoMock->method('toExternal')->willReturn([
            'uuid' => 'veiculo-uuid-456',
            'marca' => 'Fiat',
            'modelo' => 'Uno',
            'placa' => 'ABC1234',
            'ano' => 2020,
        ]);

        return $veiculoMock;
    }

    public function testCriarEntidadeComDadosValidos()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();
        $dtAbertura = new DateTimeImmutable();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: $dtAbertura,
            descricao: 'Troca de óleo',
            status: Entidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: []
        );

        $this->assertEquals('ordem-uuid-789', $entidade->uuid);
        $this->assertSame($cliente, $entidade->cliente);
        $this->assertSame($veiculo, $entidade->veiculo);
        $this->assertEquals($dtAbertura, $entidade->dtAbertura);
        $this->assertEquals('Troca de óleo', $entidade->descricao);
        $this->assertEquals(Entidade::STATUS_RECEBIDA, $entidade->status);
        $this->assertEquals([], $entidade->servicos);
        $this->assertEquals([], $entidade->materiais);
        $this->assertNull($entidade->dtFinalizacao);
        $this->assertNull($entidade->dtAtualizacao);
    }

    public function testCriarEntidadeComStatusPadrao()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable()
        );

        $this->assertEquals(Entidade::STATUS_RECEBIDA, $entidade->status);
    }

    public function testEncerrarOrdem()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_EM_EXECUCAO
        );

        $this->assertEquals(Entidade::STATUS_EM_EXECUCAO, $entidade->status);
        $this->assertNull($entidade->dtAtualizacao);

        $entidade->encerrar();

        $this->assertEquals(Entidade::STATUS_FINALIZADA, $entidade->status);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAtualizacao);
    }

    public function testAtualizarOrdem()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            descricao: 'Descrição original'
        );

        $this->assertNull($entidade->dtAtualizacao);

        $novosDados = [
            'descricao' => 'Nova descrição',
            'status' => Entidade::STATUS_EM_DIAGNOSTICO
        ];

        $entidade->atualizar($novosDados);

        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAtualizacao);
    }

    public function testToExternalComServicosVazios()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();
        $dtAbertura = new DateTimeImmutable('2025-01-15 14:30:00');

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: $dtAbertura,
            descricao: 'Revisão completa',
            status: Entidade::STATUS_RECEBIDA,
            servicos: [],
            materiais: []
        );

        $resultado = $entidade->toExternal();

        $this->assertIsArray($resultado);
        $this->assertEquals('ordem-uuid-789', $resultado['uuid']);
        $this->assertEquals('Revisão completa', $resultado['descricao']);
        $this->assertEquals(Entidade::STATUS_RECEBIDA, $resultado['status']);
        $this->assertIsArray($resultado['cliente']);
        $this->assertIsArray($resultado['veiculo']);
        $this->assertEquals([], $resultado['servicos']);
        $this->assertEquals([], $resultado['materiais']);
        $this->assertEquals('2025-01-15 14:30:00', $resultado['dt_abertura']);
        $this->assertNull($resultado['dt_finalizacao']);
        $this->assertNull($resultado['dt_atualizacao']);
        $this->assertEquals(0.0, $resultado['total_materiais']);
        $this->assertEquals(0.0, $resultado['total_servicos']);
        $this->assertEquals(0.0, $resultado['total_geral']);
    }

    public function testToExternalComServicosEMateriais()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();
        $dtAbertura = new DateTimeImmutable('2025-01-15 14:30:00');

        $servicos = [
            [
                'uuid' => 'servico-uuid-1',
                'nome' => 'Troca de óleo',
                'valor' => 15000, // R$ 150,00 em centavos
            ],
            [
                'uuid' => 'servico-uuid-2',
                'nome' => 'Alinhamento',
                'valor' => 8000, // R$ 80,00 em centavos
            ],
        ];

        $materiais = [
            [
                'uuid' => 'material-uuid-1',
                'nome' => 'Óleo 5W30',
                'preco_uso_interno' => 12000, // R$ 120,00 em centavos
            ],
            [
                'uuid' => 'material-uuid-2',
                'nome' => 'Filtro de óleo',
                'preco_uso_interno' => 3500, // R$ 35,00 em centavos
            ],
        ];

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: $dtAbertura,
            descricao: 'Troca de óleo completa',
            status: Entidade::STATUS_EM_EXECUCAO,
            servicos: $servicos,
            materiais: $materiais
        );

        $resultado = $entidade->toExternal();

        // Verificar servicos
        $this->assertCount(2, $resultado['servicos']);
        $this->assertEquals('servico-uuid-1', $resultado['servicos'][0]['uuid']);
        $this->assertEquals('Troca de óleo', $resultado['servicos'][0]['nome']);
        $this->assertEquals(150.0, $resultado['servicos'][0]['valor']);
        $this->assertEquals('servico-uuid-2', $resultado['servicos'][1]['uuid']);
        $this->assertEquals('Alinhamento', $resultado['servicos'][1]['nome']);
        $this->assertEquals(80.0, $resultado['servicos'][1]['valor']);

        // Verificar materiais
        $this->assertCount(2, $resultado['materiais']);
        $this->assertEquals('material-uuid-1', $resultado['materiais'][0]['uuid']);
        $this->assertEquals('Óleo 5W30', $resultado['materiais'][0]['nome']);
        $this->assertEquals(120.0, $resultado['materiais'][0]['valor']);
        $this->assertEquals('material-uuid-2', $resultado['materiais'][1]['uuid']);
        $this->assertEquals('Filtro de óleo', $resultado['materiais'][1]['nome']);
        $this->assertEquals(35.0, $resultado['materiais'][1]['valor']);

        // Verificar totais
        $this->assertEquals(155.0, $resultado['total_materiais']); // 120 + 35
        $this->assertEquals(230.0, $resultado['total_servicos']); // 150 + 80
        $this->assertEquals(385.0, $resultado['total_geral']); // 155 + 230
    }

    public function testToExternalComTodasAsDatas()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();
        $dtAbertura = new DateTimeImmutable('2025-01-15 10:00:00');
        $dtFinalizacao = new DateTimeImmutable('2025-01-20 16:30:00');
        $dtAtualizacao = new DateTimeImmutable('2025-01-18 14:15:00');

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: $dtAbertura,
            descricao: 'Revisão completa',
            status: Entidade::STATUS_FINALIZADA,
            servicos: [],
            materiais: [],
            dtFinalizacao: $dtFinalizacao,
            dtAtualizacao: $dtAtualizacao
        );

        $resultado = $entidade->toExternal();

        $this->assertEquals('2025-01-15 10:00:00', $resultado['dt_abertura']);
        $this->assertEquals('2025-01-20 16:30:00', $resultado['dt_finalizacao']);
        $this->assertEquals('2025-01-18 14:15:00', $resultado['dt_atualizacao']);
    }

    public function testStatusRecebida()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_RECEBIDA
        );

        $this->assertEquals(Entidade::STATUS_RECEBIDA, $entidade->status);
    }

    public function testStatusEmDiagnostico()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_EM_DIAGNOSTICO
        );

        $this->assertEquals(Entidade::STATUS_EM_DIAGNOSTICO, $entidade->status);
    }

    public function testStatusAguardandoAprovacao()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_AGUARDANDO_APROVACAO
        );

        $this->assertEquals(Entidade::STATUS_AGUARDANDO_APROVACAO, $entidade->status);
    }

    public function testStatusAprovada()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_APROVADA
        );

        $this->assertEquals(Entidade::STATUS_APROVADA, $entidade->status);
    }

    public function testStatusReprovada()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_REPROVADA
        );

        $this->assertEquals(Entidade::STATUS_REPROVADA, $entidade->status);
    }

    public function testStatusCancelada()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_CANCELADA
        );

        $this->assertEquals(Entidade::STATUS_CANCELADA, $entidade->status);
    }

    public function testStatusEmExecucao()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_EM_EXECUCAO
        );

        $this->assertEquals(Entidade::STATUS_EM_EXECUCAO, $entidade->status);
    }

    public function testStatusFinalizada()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_FINALIZADA
        );

        $this->assertEquals(Entidade::STATUS_FINALIZADA, $entidade->status);
    }

    public function testStatusEntregue()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            status: Entidade::STATUS_ENTREGUE
        );

        $this->assertEquals(Entidade::STATUS_ENTREGUE, $entidade->status);
    }

    public function testToExternalComApenasServicos()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $servicos = [
            [
                'uuid' => 'servico-uuid-1',
                'nome' => 'Revisão',
                'valor' => 25000, // R$ 250,00
            ],
        ];

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            servicos: $servicos,
            materiais: []
        );

        $resultado = $entidade->toExternal();

        $this->assertEquals(0.0, $resultado['total_materiais']);
        $this->assertEquals(250.0, $resultado['total_servicos']);
        $this->assertEquals(250.0, $resultado['total_geral']);
    }

    public function testToExternalComApenasMateriais()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $materiais = [
            [
                'uuid' => 'material-uuid-1',
                'nome' => 'Peça',
                'preco_uso_interno' => 45000, // R$ 450,00
            ],
        ];

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            servicos: [],
            materiais: $materiais
        );

        $resultado = $entidade->toExternal();

        $this->assertEquals(450.0, $resultado['total_materiais']);
        $this->assertEquals(0.0, $resultado['total_servicos']);
        $this->assertEquals(450.0, $resultado['total_geral']);
    }

    public function testToExternalVerificaClienteEVeiculo()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            servicos: [],
            materiais: []
        );

        $resultado = $entidade->toExternal();

        // Verificar estrutura do cliente
        $this->assertArrayHasKey('cliente', $resultado);
        $this->assertEquals('cliente-uuid-123', $resultado['cliente']['uuid']);
        $this->assertEquals('João Silva', $resultado['cliente']['nome']);
        $this->assertEquals('12345678901', $resultado['cliente']['documento']);

        // Verificar estrutura do veículo
        $this->assertArrayHasKey('veiculo', $resultado);
        $this->assertEquals('veiculo-uuid-456', $resultado['veiculo']['uuid']);
        $this->assertEquals('Fiat', $resultado['veiculo']['marca']);
        $this->assertEquals('Uno', $resultado['veiculo']['modelo']);
        $this->assertEquals('ABC1234', $resultado['veiculo']['placa']);
    }

    public function testToExternalComVariosServicosEMateriais()
    {
        $cliente = $this->criarClienteMock();
        $veiculo = $this->criarVeiculoMock();

        $servicos = [
            ['uuid' => 's1', 'nome' => 'Serviço 1', 'valor' => 10000],
            ['uuid' => 's2', 'nome' => 'Serviço 2', 'valor' => 20000],
            ['uuid' => 's3', 'nome' => 'Serviço 3', 'valor' => 30000],
        ];

        $materiais = [
            ['uuid' => 'm1', 'nome' => 'Material 1', 'preco_uso_interno' => 5000],
            ['uuid' => 'm2', 'nome' => 'Material 2', 'preco_uso_interno' => 15000],
            ['uuid' => 'm3', 'nome' => 'Material 3', 'preco_uso_interno' => 25000],
        ];

        $entidade = new Entidade(
            uuid: 'ordem-uuid-789',
            cliente: $cliente,
            veiculo: $veiculo,
            dtAbertura: new DateTimeImmutable(),
            servicos: $servicos,
            materiais: $materiais
        );

        $resultado = $entidade->toExternal();

        $this->assertCount(3, $resultado['servicos']);
        $this->assertCount(3, $resultado['materiais']);
        $this->assertEquals(450.0, $resultado['total_materiais']); // 50 + 150 + 250
        $this->assertEquals(600.0, $resultado['total_servicos']); // 100 + 200 + 300
        $this->assertEquals(1050.0, $resultado['total_geral']); // 450 + 600
    }
}
