<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Dto;

use App\Domain\Entity\Usuario\Perfil;
use App\Infrastructure\Dto\UsuarioDto;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UsuarioDtoTest extends TestCase
{
    public function testConstructorComTodosOsParametros()
    {
        $criadoEm = new DateTimeImmutable('2025-01-01 10:00:00');
        $atualizadoEm = new DateTimeImmutable('2025-01-01 10:00:00');
        $deletadoEm = new DateTimeImmutable('2025-01-02 10:00:00');

        $dto = new UsuarioDto(
            id: '1',
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'hashed-senha',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criado_em: $criadoEm,
            atualizado_em: $atualizadoEm,
            deletado_em: $deletadoEm,
        );

        $this->assertInstanceOf(UsuarioDto::class, $dto);
        $this->assertEquals('1', $dto->id);
        $this->assertEquals('uuid-123', $dto->uuid);
        $this->assertEquals('João Silva', $dto->nome);
        $this->assertEquals('joao@example.com', $dto->email);
        $this->assertEquals('hashed-senha', $dto->senha);
        $this->assertTrue($dto->ativo);
        $this->assertEquals(Perfil::ATENDENTE->value, $dto->perfil);
        $this->assertEquals($criadoEm, $dto->criado_em);
        $this->assertEquals($atualizadoEm, $dto->atualizado_em);
        $this->assertEquals($deletadoEm, $dto->deletado_em);
    }

    public function testConstructorComParametrosOpcionaisNulos()
    {
        $dto = new UsuarioDto();

        $this->assertInstanceOf(UsuarioDto::class, $dto);
        $this->assertNull($dto->id);
        $this->assertNull($dto->uuid);
        $this->assertNull($dto->nome);
        $this->assertNull($dto->email);
        $this->assertNull($dto->senha);
        $this->assertNull($dto->ativo);
        $this->assertNull($dto->perfil);
        $this->assertNull($dto->criado_em);
        $this->assertNull($dto->atualizado_em);
        $this->assertNull($dto->deletado_em);
    }

    public function testConstructorComAtivoFalso()
    {
        $dto = new UsuarioDto(
            ativo: false,
            perfil: Perfil::MECANICO->value,
        );

        $this->assertFalse($dto->ativo);
        $this->assertEquals(Perfil::MECANICO->value, $dto->perfil);
    }
}
