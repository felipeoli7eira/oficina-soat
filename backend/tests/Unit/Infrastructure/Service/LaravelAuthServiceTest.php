<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\Perfil;
use App\Domain\Entity\Usuario\RepositorioInterface;
use App\Infrastructure\Service\LaravelAuthService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class LaravelAuthServiceTest extends TestCase
{
    private function criarEntidade(bool $ativo = true): Entidade
    {
        return new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: password_hash('senha123', PASSWORD_BCRYPT),
            ativo: $ativo,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );
    }

    public function testAttemptComCredenciaisValidas()
    {
        $entidade = $this->criarEntidade();

        $repositorio = $this->createMock(RepositorioInterface::class);
        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('joao@example.com', 'email')
            ->willReturn($entidade);

        $service = new LaravelAuthService($repositorio);
        $resultado = $service->attempt('joao@example.com', 'senha123');

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('joao@example.com', $resultado->email);
    }

    public function testAttemptComSenhaIncorreta()
    {
        $entidade = $this->criarEntidade();

        $repositorio = $this->createMock(RepositorioInterface::class);
        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidade);

        $service = new LaravelAuthService($repositorio);
        $resultado = $service->attempt('joao@example.com', 'senha-errada');

        $this->assertNull($resultado);
    }

    public function testAttemptComUsuarioNaoEncontrado()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);
        $repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $service = new LaravelAuthService($repositorio);
        $resultado = $service->attempt('naoexiste@example.com', 'senha123');

        $this->assertNull($resultado);
    }
}
