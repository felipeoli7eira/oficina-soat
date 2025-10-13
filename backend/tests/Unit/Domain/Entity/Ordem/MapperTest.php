<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Ordem;

use App\Domain\Entity\Ordem\Entidade;
use App\Domain\Entity\Ordem\Mapper;
use App\Models\ClienteModel;
use App\Models\MaterialModel;
use App\Models\OrdemModel;
use App\Models\ServicoModel;
use App\Models\VeiculoModel;
use DateTimeImmutable;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Mockery;

class MapperTest extends TestCase
{
    private Mapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new Mapper();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createClienteMock(
        string $uuid,
        string $nome,
        string $documento,
        string $email,
        string $fone,
        string $criadoEm,
        string $atualizadoEm
    ): ClienteModel {
        $cliente = Mockery::mock(ClienteModel::class)->makePartial();
        $cliente->shouldAllowMockingProtectedMethods();
        $cliente->uuid = $uuid;
        $cliente->nome = $nome;
        $cliente->documento = $documento;
        $cliente->email = $email;
        $cliente->fone = $fone;
        $cliente->criado_em = $criadoEm;
        $cliente->atualizado_em = $atualizadoEm;
        return $cliente;
    }

    private function createVeiculoMock(
        string $uuid,
        string $marca,
        string $modelo,
        string $placa,
        int $ano,
        int $clienteId,
        string $criadoEm,
        string $atualizadoEm
    ): VeiculoModel {
        $veiculo = Mockery::mock(VeiculoModel::class)->makePartial();
        $veiculo->shouldAllowMockingProtectedMethods();
        $veiculo->uuid = $uuid;
        $veiculo->marca = $marca;
        $veiculo->modelo = $modelo;
        $veiculo->placa = $placa;
        $veiculo->ano = $ano;
        $veiculo->cliente_id = $clienteId;
        $veiculo->criado_em = $criadoEm;
        $veiculo->atualizado_em = $atualizadoEm;
        return $veiculo;
    }

    private function createServicoMock(string $uuid, string $nome, int $valor): object
    {
        $servico = new \stdClass();
        $servico->uuid = $uuid;
        $servico->nome = $nome;
        $servico->valor = $valor;
        return $servico;
    }

    private function createMaterialMock(string $uuid, string $nome, int $precoUsoInterno): object
    {
        $material = new \stdClass();
        $material->uuid = $uuid;
        $material->nome = $nome;
        $material->preco_uso_interno = $precoUsoInterno;
        return $material;
    }

    private function createOrdemMock(
        string $uuid,
        ClienteModel $cliente,
        VeiculoModel $veiculo,
        string $descricao,
        string $status,
        string $dtAbertura,
        ?string $dtFinalizacao,
        ?string $dtAtualizacao,
        Collection $servicos,
        Collection $materiais
    ): OrdemModel {
        $ordem = Mockery::mock(OrdemModel::class)->makePartial();
        $ordem->shouldAllowMockingProtectedMethods();
        $ordem->uuid = $uuid;
        $ordem->cliente = $cliente;
        $ordem->veiculo = $veiculo;
        $ordem->descricao = $descricao;
        $ordem->status = $status;
        $ordem->dt_abertura = $dtAbertura;
        $ordem->dt_finalizacao = $dtFinalizacao;
        $ordem->dt_atualizacao = $dtAtualizacao;
        $ordem->servicos = $servicos;
        $ordem->materiais = $materiais;
        return $ordem;
    }

    public function testFromModelToEntityComDatasCompletas()
    {
        // Arrange
        $cliente = $this->createClienteMock(
            'cliente-uuid-123',
            'João Silva',
            '12345678901',
            'joao@example.com',
            '11999999999',
            '2025-01-01 10:00:00',
            '2025-01-02 15:30:00'
        );

        $veiculo = $this->createVeiculoMock(
            'veiculo-uuid-456',
            'Toyota',
            'Corolla',
            'ABC-1234',
            2023,
            1,
            '2025-01-03 08:00:00',
            '2025-01-03 08:00:00'
        );

        $servico1 = $this->createServicoMock('servico-uuid-1', 'Troca de óleo', 15000);
        $servico2 = $this->createServicoMock('servico-uuid-2', 'Alinhamento', 8000);
        $servicosCollection = new Collection([$servico1, $servico2]);

        $material1 = $this->createMaterialMock('material-uuid-1', 'Óleo sintético', 12000);
        $material2 = $this->createMaterialMock('material-uuid-2', 'Filtro de óleo', 3500);
        $materiaisCollection = new Collection([$material1, $material2]);

        $ordem = $this->createOrdemMock(
            'ordem-uuid-789',
            $cliente,
            $veiculo,
            'Manutenção preventiva',
            Entidade::STATUS_EM_EXECUCAO,
            '2025-01-10 09:00:00',
            '2025-01-12 17:00:00',
            '2025-01-11 14:30:00',
            $servicosCollection,
            $materiaisCollection
        );

        // Act
        $entidade = $this->mapper->fromModelToEntity($ordem);

        // Assert
        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('ordem-uuid-789', $entidade->uuid);
        $this->assertEquals('Manutenção preventiva', $entidade->descricao);
        $this->assertEquals(Entidade::STATUS_EM_EXECUCAO, $entidade->status);

        // Assert - Cliente
        $this->assertEquals('cliente-uuid-123', $entidade->cliente->uuid);
        $this->assertEquals('João Silva', $entidade->cliente->nome);
        $this->assertEquals('12345678901', $entidade->cliente->documento);
        $this->assertEquals('joao@example.com', $entidade->cliente->email);

        // Assert - Veículo
        $this->assertEquals('veiculo-uuid-456', $entidade->veiculo->uuid);
        $this->assertEquals('Toyota', $entidade->veiculo->marca);
        $this->assertEquals('Corolla', $entidade->veiculo->modelo);
        $this->assertEquals('ABC-1234', $entidade->veiculo->placa);
        $this->assertEquals(2023, $entidade->veiculo->ano);

        // Assert - Datas
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAbertura);
        $this->assertEquals('2025-01-10 09:00:00', $entidade->dtAbertura->format('Y-m-d H:i:s'));

        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtFinalizacao);
        $this->assertEquals('2025-01-12 17:00:00', $entidade->dtFinalizacao->format('Y-m-d H:i:s'));

        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAtualizacao);
        $this->assertEquals('2025-01-11 14:30:00', $entidade->dtAtualizacao->format('Y-m-d H:i:s'));

        // Assert - Serviços
        $this->assertIsArray($entidade->servicos);
        $this->assertCount(2, $entidade->servicos);
        $this->assertEquals('servico-uuid-1', $entidade->servicos[0]->uuid);
        $this->assertEquals('Troca de óleo', $entidade->servicos[0]->nome);
        $this->assertEquals(15000, $entidade->servicos[0]->valor);

        // Assert - Materiais
        $this->assertIsArray($entidade->materiais);
        $this->assertCount(2, $entidade->materiais);
        $this->assertEquals('material-uuid-1', $entidade->materiais[0]->uuid);
        $this->assertEquals('Óleo sintético', $entidade->materiais[0]->nome);
        $this->assertEquals(12000, $entidade->materiais[0]->preco_uso_interno);
    }

    public function testFromModelToEntityComDtFinalizacaoNull()
    {
        // Arrange
        $cliente = $this->createClienteMock(
            'cliente-uuid-123',
            'Maria Santos',
            '98765432109',
            'maria@example.com',
            '11988888888',
            '2025-01-01 10:00:00',
            '2025-01-02 15:30:00'
        );

        $veiculo = $this->createVeiculoMock(
            'veiculo-uuid-456',
            'Honda',
            'Civic',
            'XYZ-9876',
            2022,
            1,
            '2025-01-03 08:00:00',
            '2025-01-03 08:00:00'
        );

        $servicosCollection = new Collection([]);
        $materiaisCollection = new Collection([]);

        $ordem = $this->createOrdemMock(
            'ordem-uuid-abc',
            $cliente,
            $veiculo,
            'Diagnóstico de problema',
            Entidade::STATUS_EM_DIAGNOSTICO,
            '2025-01-15 10:00:00',
            null, // Data de finalização é null
            '2025-01-16 11:00:00',
            $servicosCollection,
            $materiaisCollection
        );

        // Act
        $entidade = $this->mapper->fromModelToEntity($ordem);

        // Assert
        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('ordem-uuid-abc', $entidade->uuid);
        $this->assertEquals(Entidade::STATUS_EM_DIAGNOSTICO, $entidade->status);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAbertura);
        $this->assertNull($entidade->dtFinalizacao);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAtualizacao);
    }

    public function testFromModelToEntityComDtAtualizacaoNull()
    {
        // Arrange
        $cliente = $this->createClienteMock(
            'cliente-uuid-999',
            'Pedro Oliveira',
            '11122233344',
            'pedro@example.com',
            '11977777777',
            '2025-01-01 10:00:00',
            '2025-01-02 15:30:00'
        );

        $veiculo = $this->createVeiculoMock(
            'veiculo-uuid-888',
            'Volkswagen',
            'Gol',
            'DEF-5678',
            2020,
            1,
            '2025-01-03 08:00:00',
            '2025-01-03 08:00:00'
        );

        $servicosCollection = new Collection([]);
        $materiaisCollection = new Collection([]);

        $ordem = $this->createOrdemMock(
            'ordem-uuid-def',
            $cliente,
            $veiculo,
            'Ordem recém criada',
            Entidade::STATUS_RECEBIDA,
            '2025-01-20 08:00:00',
            null,
            null, // Data de atualização é null
            $servicosCollection,
            $materiaisCollection
        );

        // Act
        $entidade = $this->mapper->fromModelToEntity($ordem);

        // Assert
        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('ordem-uuid-def', $entidade->uuid);
        $this->assertEquals(Entidade::STATUS_RECEBIDA, $entidade->status);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAbertura);
        $this->assertNull($entidade->dtFinalizacao);
        $this->assertNull($entidade->dtAtualizacao);
    }

    public function testFromModelToEntityComServicosEMateriaisVazios()
    {
        // Arrange
        $cliente = $this->createClienteMock(
            'cliente-uuid-empty',
            'Ana Costa',
            '55566677788',
            'ana@example.com',
            '11966666666',
            '2025-01-01 10:00:00',
            '2025-01-02 15:30:00'
        );

        $veiculo = $this->createVeiculoMock(
            'veiculo-uuid-empty',
            'Fiat',
            'Uno',
            'GHI-1122',
            2019,
            1,
            '2025-01-03 08:00:00',
            '2025-01-03 08:00:00'
        );

        $servicosCollection = new Collection([]); // Coleção vazia
        $materiaisCollection = new Collection([]); // Coleção vazia

        $ordem = $this->createOrdemMock(
            'ordem-uuid-empty',
            $cliente,
            $veiculo,
            'Avaliação inicial',
            Entidade::STATUS_AGUARDANDO_APROVACAO,
            '2025-01-25 14:00:00',
            null,
            null,
            $servicosCollection,
            $materiaisCollection
        );

        // Act
        $entidade = $this->mapper->fromModelToEntity($ordem);

        // Assert
        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('ordem-uuid-empty', $entidade->uuid);
        $this->assertEquals('Avaliação inicial', $entidade->descricao);
        $this->assertIsArray($entidade->servicos);
        $this->assertCount(0, $entidade->servicos);
        $this->assertIsArray($entidade->materiais);
        $this->assertCount(0, $entidade->materiais);
    }

    public function testFromModelToEntityComMultiplosServicosEMateriais()
    {
        // Arrange
        $cliente = $this->createClienteMock(
            'cliente-uuid-multi',
            'Carlos Alberto',
            '99988877766',
            'carlos@example.com',
            '11955555555',
            '2025-01-01 10:00:00',
            '2025-01-02 15:30:00'
        );

        $veiculo = $this->createVeiculoMock(
            'veiculo-uuid-multi',
            'Chevrolet',
            'Onix',
            'JKL-3344',
            2024,
            1,
            '2025-01-03 08:00:00',
            '2025-01-03 08:00:00'
        );

        // Criar 3 serviços
        $servico1 = $this->createServicoMock('servico-uuid-m1', 'Revisão completa', 35000);
        $servico2 = $this->createServicoMock('servico-uuid-m2', 'Balanceamento', 12000);
        $servico3 = $this->createServicoMock('servico-uuid-m3', 'Geometria', 15000);
        $servicosCollection = new Collection([$servico1, $servico2, $servico3]);

        // Criar 4 materiais
        $material1 = $this->createMaterialMock('material-uuid-m1', 'Óleo mineral', 8000);
        $material2 = $this->createMaterialMock('material-uuid-m2', 'Filtro de ar', 4500);
        $material3 = $this->createMaterialMock('material-uuid-m3', 'Velas de ignição', 12000);
        $material4 = $this->createMaterialMock('material-uuid-m4', 'Fluido de freio', 6500);
        $materiaisCollection = new Collection([$material1, $material2, $material3, $material4]);

        $ordem = $this->createOrdemMock(
            'ordem-uuid-multi',
            $cliente,
            $veiculo,
            'Manutenção completa com múltiplos serviços',
            Entidade::STATUS_FINALIZADA,
            '2025-01-30 08:00:00',
            '2025-02-02 18:00:00',
            '2025-02-01 12:00:00',
            $servicosCollection,
            $materiaisCollection
        );

        // Act
        $entidade = $this->mapper->fromModelToEntity($ordem);

        // Assert
        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('ordem-uuid-multi', $entidade->uuid);
        $this->assertEquals(Entidade::STATUS_FINALIZADA, $entidade->status);

        // Assert - Verificar quantidade de serviços
        $this->assertIsArray($entidade->servicos);
        $this->assertCount(3, $entidade->servicos);
        $this->assertEquals('servico-uuid-m1', $entidade->servicos[0]->uuid);
        $this->assertEquals('servico-uuid-m2', $entidade->servicos[1]->uuid);
        $this->assertEquals('servico-uuid-m3', $entidade->servicos[2]->uuid);

        // Assert - Verificar quantidade de materiais
        $this->assertIsArray($entidade->materiais);
        $this->assertCount(4, $entidade->materiais);
        $this->assertEquals('material-uuid-m1', $entidade->materiais[0]->uuid);
        $this->assertEquals('material-uuid-m2', $entidade->materiais[1]->uuid);
        $this->assertEquals('material-uuid-m3', $entidade->materiais[2]->uuid);
        $this->assertEquals('material-uuid-m4', $entidade->materiais[3]->uuid);

        // Assert - Verificar valores dos serviços
        $this->assertEquals(35000, $entidade->servicos[0]->valor);
        $this->assertEquals(12000, $entidade->servicos[1]->valor);
        $this->assertEquals(15000, $entidade->servicos[2]->valor);

        // Assert - Verificar valores dos materiais
        $this->assertEquals(8000, $entidade->materiais[0]->preco_uso_interno);
        $this->assertEquals(4500, $entidade->materiais[1]->preco_uso_interno);
        $this->assertEquals(12000, $entidade->materiais[2]->preco_uso_interno);
        $this->assertEquals(6500, $entidade->materiais[3]->preco_uso_interno);
    }

    public function testFromModelToEntityComOrdemAprovada()
    {
        // Arrange
        $cliente = $this->createClienteMock(
            'cliente-uuid-aprovado',
            'Beatriz Lima',
            '44433322211',
            'beatriz@example.com',
            '11944444444',
            '2025-01-01 10:00:00',
            '2025-01-02 15:30:00'
        );

        $veiculo = $this->createVeiculoMock(
            'veiculo-uuid-aprovado',
            'Renault',
            'Sandero',
            'MNO-5566',
            2021,
            1,
            '2025-01-03 08:00:00',
            '2025-01-03 08:00:00'
        );

        $servico = $this->createServicoMock('servico-uuid-ap', 'Troca de pastilhas de freio', 28000);
        $servicosCollection = new Collection([$servico]);

        $material = $this->createMaterialMock('material-uuid-ap', 'Pastilhas de freio', 18000);
        $materiaisCollection = new Collection([$material]);

        $ordem = $this->createOrdemMock(
            'ordem-uuid-aprovada',
            $cliente,
            $veiculo,
            'Substituição de freios',
            Entidade::STATUS_APROVADA,
            '2025-02-05 09:00:00',
            null,
            '2025-02-06 10:30:00',
            $servicosCollection,
            $materiaisCollection
        );

        // Act
        $entidade = $this->mapper->fromModelToEntity($ordem);

        // Assert
        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('ordem-uuid-aprovada', $entidade->uuid);
        $this->assertEquals(Entidade::STATUS_APROVADA, $entidade->status);
        $this->assertEquals('Substituição de freios', $entidade->descricao);
        $this->assertEquals('Beatriz Lima', $entidade->cliente->nome);
        $this->assertEquals('Sandero', $entidade->veiculo->modelo);
        $this->assertCount(1, $entidade->servicos);
        $this->assertCount(1, $entidade->materiais);
    }

    public function testFromModelToEntityComDatasEmFormatosDiferentes()
    {
        // Arrange
        $cliente = $this->createClienteMock(
            'cliente-uuid-date',
            'Roberto Mendes',
            '33344455566',
            'roberto@example.com',
            '11933333333',
            '2025-03-01T08:30:00',
            '2025-03-02T14:45:00'
        );

        $veiculo = $this->createVeiculoMock(
            'veiculo-uuid-date',
            'Nissan',
            'Kicks',
            'PQR-7788',
            2023,
            1,
            '2025-03-01T09:00:00',
            '2025-03-01T09:00:00'
        );

        $servicosCollection = new Collection([]);
        $materiaisCollection = new Collection([]);

        $ordem = $this->createOrdemMock(
            'ordem-uuid-date',
            $cliente,
            $veiculo,
            'Teste de formatos de data',
            Entidade::STATUS_ENTREGUE,
            '2025-03-10T10:15:30',
            '2025-03-12T16:45:00',
            '2025-03-11T13:20:15',
            $servicosCollection,
            $materiaisCollection
        );

        // Act
        $entidade = $this->mapper->fromModelToEntity($ordem);

        // Assert
        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAbertura);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtFinalizacao);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAtualizacao);
        $this->assertEquals('2025-03-10', $entidade->dtAbertura->format('Y-m-d'));
        $this->assertEquals('2025-03-12', $entidade->dtFinalizacao->format('Y-m-d'));
        $this->assertEquals('2025-03-11', $entidade->dtAtualizacao->format('Y-m-d'));
    }

    public function testFromModelToEntityComAmbasDatasNulas()
    {
        // Arrange
        $cliente = $this->createClienteMock(
            'cliente-uuid-nulo',
            'Fernanda Souza',
            '22211133344',
            'fernanda@example.com',
            '11922222222',
            '2025-02-15 10:00:00',
            '2025-02-15 10:00:00'
        );

        $veiculo = $this->createVeiculoMock(
            'veiculo-uuid-nulo',
            'Ford',
            'Ka',
            'STU-9900',
            2018,
            1,
            '2025-02-15 10:00:00',
            '2025-02-15 10:00:00'
        );

        $servicosCollection = new Collection([]);
        $materiaisCollection = new Collection([]);

        $ordem = $this->createOrdemMock(
            'ordem-uuid-nulo',
            $cliente,
            $veiculo,
            'Ordem nova sem alterações',
            Entidade::STATUS_RECEBIDA,
            '2025-02-20 14:00:00',
            null, // dt_finalizacao null
            null, // dt_atualizacao null
            $servicosCollection,
            $materiaisCollection
        );

        // Act
        $entidade = $this->mapper->fromModelToEntity($ordem);

        // Assert
        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('ordem-uuid-nulo', $entidade->uuid);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->dtAbertura);
        $this->assertNull($entidade->dtFinalizacao);
        $this->assertNull($entidade->dtAtualizacao);
    }
}
