<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Cliente;

use App\Domain\Entity\Cliente\Entidade;
use App\Domain\Entity\Cliente\Mapper;
use App\Models\ClienteModel;
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
        $model = Mockery::mock(ClienteModel::class)->makePartial();
        $model->uuid = 'uuid-123';
        $model->nome = 'João Silva';
        $model->documento = '12345678901';
        $model->email = 'joao@example.com';
        $model->fone = '11999999999';
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-01 10:00:00';
        $model->deletado_em = null;

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('João Silva', $entidade->nome);
        $this->assertEquals('12345678901', $entidade->documento);
        $this->assertEquals('joao@example.com', $entidade->email);
        $this->assertEquals('11999999999', $entidade->fone);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->criadoEm);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->atualizadoEm);
        $this->assertNull($entidade->deletadoEm);
    }

    public function testFromModelToEntidadeComDeletadoEm()
    {
        $model = Mockery::mock(ClienteModel::class)->makePartial();
        $model->uuid = 'uuid-456';
        $model->nome = 'Maria Santos';
        $model->documento = '98765432100';
        $model->email = 'maria@example.com';
        $model->fone = '11988888888';
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-02 10:00:00';
        $model->deletado_em = '2025-01-03 10:00:00';

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->deletadoEm);
    }
}
