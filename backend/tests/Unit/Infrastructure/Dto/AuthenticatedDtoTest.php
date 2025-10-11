<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Dto;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\Perfil;
use App\Infrastructure\Dto\AuthenticatedDto;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AuthenticatedDtoTest extends TestCase
{
    public function testToAssociativeArray()
    {
        $usuario = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $dto = new AuthenticatedDto(
            usuario: $usuario,
            token: 'token-jwt-123',
            tokenType: 'Bearer'
        );

        $resultado = $dto->toAssociativeArray();

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('user', $resultado);
        $this->assertArrayHasKey('token', $resultado);
        $this->assertArrayHasKey('token_type', $resultado);
        $this->assertEquals('token-jwt-123', $resultado['token']);
        $this->assertEquals('Bearer', $resultado['token_type']);
        $this->assertIsArray($resultado['user']);
        $this->assertEquals('uuid-123', $resultado['user']['uuid']);
        $this->assertEquals('João Silva', $resultado['user']['nome']);
        $this->assertEquals('joao@example.com', $resultado['user']['email']);
    }

    public function testConstructor()
    {
        $usuario = new Entidade(
            uuid: 'uuid-456',
            nome: 'Maria Santos',
            email: 'maria@example.com',
            senha: 'senha456',
            ativo: true,
            perfil: Perfil::MECANICO->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $dto = new AuthenticatedDto(
            usuario: $usuario,
            token: 'token-xyz',
            tokenType: 'Bearer'
        );

        $this->assertInstanceOf(AuthenticatedDto::class, $dto);
        $this->assertEquals($usuario, $dto->usuario);
        $this->assertEquals('token-xyz', $dto->token);
        $this->assertEquals('Bearer', $dto->tokenType);
    }
}
