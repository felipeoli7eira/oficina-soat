<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\Mapper;
use App\Models\ServicoModel;
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
        $model = Mockery::mock(ServicoModel::class)->makePartial();
        $model->uuid = 'uuid-123';
        $model->nome = 'Troca de Óleo';
        $model->valor = 5000;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-01 10:00:00';
        $model->deletado_em = null;

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('Troca de Óleo', $entidade->nome);
        $this->assertEquals(5000, $entidade->valor);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->criadoEm);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->atualizadoEm);
        $this->assertNull($entidade->deletadoEm);
    }

    public function testFromModelToEntidadeComDeletadoEm()
    {
        $model = Mockery::mock(ServicoModel::class)->makePartial();
        $model->uuid = 'uuid-456';
        $model->nome = 'Alinhamento';
        $model->valor = 8000;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-02 10:00:00';
        $model->deletado_em = '2025-01-03 10:00:00';

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->deletadoEm);
    }
}
