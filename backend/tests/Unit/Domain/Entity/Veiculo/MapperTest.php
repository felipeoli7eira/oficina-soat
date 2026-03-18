<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Veiculo;

use App\Domain\Entity\Veiculo\Entidade;
use App\Domain\Entity\Veiculo\Mapper;
use App\Models\VeiculoModel;
use DateTimeImmutable;
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

    public function testFromModelToEntidade()
    {
        $model = Mockery::mock(VeiculoModel::class)->makePartial();
        $model->uuid = 'uuid-123';
        $model->marca = 'Toyota';
        $model->modelo = 'Corolla';
        $model->placa = 'ABC1234';
        $model->ano = 2020;
        $model->cliente_id = 1;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-01 10:00:00';
        $model->deletado_em = null;

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('Toyota', $entidade->marca);
        $this->assertEquals('Corolla', $entidade->modelo);
        $this->assertEquals('ABC1234', $entidade->placa);
        $this->assertEquals(2020, $entidade->ano);
        $this->assertEquals(1, $entidade->clienteId);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->criadoEm);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->atualizadoEm);
        $this->assertNull($entidade->deletadoEm);
    }

    public function testFromModelToEntidadeComDeletadoEm()
    {
        $model = Mockery::mock(VeiculoModel::class)->makePartial();
        $model->uuid = 'uuid-456';
        $model->marca = 'Honda';
        $model->modelo = 'Civic';
        $model->placa = 'XYZ5678';
        $model->ano = 2019;
        $model->cliente_id = 2;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-02 10:00:00';
        $model->deletado_em = '2025-01-03 10:00:00';

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->deletadoEm);
    }
}
