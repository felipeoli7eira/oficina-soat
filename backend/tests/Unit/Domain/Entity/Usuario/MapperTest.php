<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\Mapper;
use App\Domain\Entity\Usuario\Perfil;
use App\Infrastructure\Dto\UsuarioDto;
use App\Models\UsuarioModel;
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
        $model = Mockery::mock(UsuarioModel::class)->makePartial();
        $model->uuid = 'uuid-123';
        $model->nome = 'João Silva';
        $model->email = 'joao@example.com';
        $model->senha = password_hash('senha123', PASSWORD_BCRYPT);
        $model->ativo = true;
        $model->perfil = Perfil::ATENDENTE->value;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-01 10:00:00';
        $model->deletado_em = null;

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('João Silva', $entidade->nome);
        $this->assertEquals('joao@example.com', $entidade->email);
        $this->assertTrue($entidade->ativo);
        $this->assertEquals(Perfil::ATENDENTE->value, $entidade->perfil);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->criadoEm);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->atualizadoEm);
        $this->assertNull($entidade->deletadoEm);
    }

    public function testFromModelToEntidadeComDeletadoEm()
    {
        $model = Mockery::mock(UsuarioModel::class)->makePartial();
        $model->uuid = 'uuid-456';
        $model->nome = 'Maria Santos';
        $model->email = 'maria@example.com';
        $model->senha = password_hash('senha456', PASSWORD_BCRYPT);
        $model->ativo = false;
        $model->perfil = Perfil::MECANICO->value;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-02 10:00:00';
        $model->deletado_em = '2025-01-03 10:00:00';

        $entidade = $this->mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertFalse($entidade->ativo);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->deletadoEm);
    }

    public function testFromEntityToModel()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: password_hash('senha123', PASSWORD_BCRYPT),
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2025-01-01 10:00:00'),
        );

        $model = $this->mapper->fromEntityToModel($entidade);

        $this->assertInstanceOf(UsuarioModel::class, $model);
        $this->assertEquals('uuid-123', $model->uuid);
        $this->assertEquals('João Silva', $model->nome);
        $this->assertEquals('joao@example.com', $model->email);
        $this->assertTrue($model->ativo);
        $this->assertEquals(Perfil::ATENDENTE->value, $model->perfil);
    }

    public function testFromModelToArray()
    {
        $model = Mockery::mock(UsuarioModel::class)->makePartial();
        $model->uuid = 'uuid-123';
        $model->nome = 'João Silva';
        $model->email = 'joao@example.com';
        $model->senha = 'hashed-senha';
        $model->ativo = true;
        $model->perfil = Perfil::ATENDENTE->value;
        $model->criado_em = '2025-01-01 10:00:00';
        $model->atualizado_em = '2025-01-01 10:00:00';
        $model->deletado_em = null;

        $array = $this->mapper->fromModelToArray($model);

        $this->assertIsArray($array);
        $this->assertEquals('uuid-123', $array['uuid']);
        $this->assertEquals('João Silva', $array['nome']);
        $this->assertEquals('joao@example.com', $array['email']);
        $this->assertTrue($array['ativo']);
        $this->assertEquals(Perfil::ATENDENTE->value, $array['perfil']);
    }

    public function testFromArrayToModel()
    {
        $array = [
            'uuid'          => 'uuid-123',
            'nome'          => 'João Silva',
            'email'         => 'joao@example.com',
            'senha'         => 'hashed-senha',
            'ativo'         => true,
            'perfil'        => Perfil::ATENDENTE->value,
            'criado_em'     => '2025-01-01 10:00:00',
            'atualizado_em' => '2025-01-01 10:00:00',
            'deletado_em'   => null,
        ];

        $model = $this->mapper->fromArrayToModel($array);

        $this->assertInstanceOf(UsuarioModel::class, $model);
        $this->assertEquals('uuid-123', $model->uuid);
        $this->assertEquals('João Silva', $model->nome);
        $this->assertEquals('joao@example.com', $model->email);
        $this->assertTrue($model->ativo);
        $this->assertEquals(Perfil::ATENDENTE->value, $model->perfil);
    }

    public function testFromDtoToEntidade()
    {
        $dto = new UsuarioDto(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: password_hash('senha123', PASSWORD_BCRYPT),
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criado_em: new DateTimeImmutable('2025-01-01 10:00:00'),
            atualizado_em: new DateTimeImmutable('2025-01-01 10:00:00'),
        );

        $entidade = $this->mapper->fromDtoToEntity($dto);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('João Silva', $entidade->nome);
        $this->assertEquals('joao@example.com', $entidade->email);
        $this->assertTrue($entidade->ativo);
        $this->assertEquals(Perfil::ATENDENTE->value, $entidade->perfil);
    }
}
