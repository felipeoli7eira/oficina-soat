<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\Mapper;
use App\Models\MaterialModel;
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
        $model = Mockery::mock(MaterialModel::class)->makePartial();
        $model->uuid = 'uuid-123';
        $model->nome = 'Óleo Motor';
        $model->gtin = '7891234567890';
        $model->estoque = 10;
        $model->sku = 'OLM-001';
        $model->descricao = 'Óleo motor 5W30';
        $model->preco_custo = 2000;
        $model->preco_venda = 3000;
        $model->preco_uso_interno = 2500;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-01 10:00:00';
        $model->deletado_em = null;

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('Óleo Motor', $entidade->nome);
        $this->assertEquals('7891234567890', $entidade->gtin);
        $this->assertEquals(10, $entidade->estoque);
        $this->assertEquals('OLM-001', $entidade->sku);
        $this->assertEquals('Óleo motor 5W30', $entidade->descricao);
        $this->assertEquals(2000, $entidade->preco_custo);
        $this->assertEquals(3000, $entidade->preco_venda);
        $this->assertEquals(2500, $entidade->preco_uso_interno);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->criadoEm);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->atualizadoEm);
        $this->assertNull($entidade->deletadoEm);
    }

    public function testFromModelToEntidadeComDeletadoEm()
    {
        $model = Mockery::mock(MaterialModel::class)->makePartial();
        $model->uuid = 'uuid-456';
        $model->nome = 'Pastilha de Freio';
        $model->gtin = '7899876543210';
        $model->estoque = 5;
        $model->sku = null;
        $model->descricao = null;
        $model->preco_custo = 5000;
        $model->preco_venda = 8000;
        $model->preco_uso_interno = 6000;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-02 10:00:00';
        $model->deletado_em = '2025-01-03 10:00:00';

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->deletadoEm);
    }
}
