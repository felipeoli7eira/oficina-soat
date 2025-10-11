<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Controller;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\Perfil;
use App\Domain\Entity\Usuario\RepositorioInterface;
use App\Domain\UseCase\Usuario\AuthenticateUseCase;
use App\Infrastructure\Controller\Usuario;
use App\Infrastructure\Dto\AuthenticatedDto;
use App\Infrastructure\Dto\UsuarioDto;
use DateTimeImmutable;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    public function testCriar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $repositorio->method('criar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'João Silva',
                'email' => 'joao@example.com',
                'perfil' => Perfil::ATENDENTE->value,
            ]);

        $dto = new UsuarioDto(
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            perfil: Perfil::ATENDENTE->value
        );

        $controller = new Usuario();

        $resultado = $controller->criar($dto, $repositorio);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('João Silva', $resultado['nome']);
        $this->assertEquals('joao@example.com', $resultado['email']);
    }

    public function testListar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $usuariosEsperados = [
            [
                'uuid' => 'uuid-1',
                'nome' => 'João Silva',
                'email' => 'joao@example.com',
                'ativo' => true,
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Maria Santos',
                'email' => 'maria@example.com',
                'ativo' => true,
            ],
        ];

        $repositorio->method('listar')
            ->willReturn($usuariosEsperados);

        $controller = new Usuario();

        $resultado = $controller->listar($repositorio);

        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
    }

    public function testDeletar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $repositorio->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $controller = new Usuario();

        $resultado = $controller->deletar('uuid-123', $repositorio);

        $this->assertTrue($resultado);
    }

    public function testAtualizar()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $entidadeExistente = $this->createMock(Entidade::class);

        $repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturn($entidadeExistente);

        $repositorio->method('atualizar')
            ->willReturn([
                'uuid' => 'uuid-123',
                'nome' => 'Novo Nome',
                'email' => 'joao@example.com',
                'senha' => 'senha123',
                'ativo' => true,
                'perfil' => Perfil::ATENDENTE->value,
                'criado_em' => '2025-01-01 10:00:00',
                'atualizado_em' => '2025-01-02 10:00:00',
                'deletado_em' => null,
            ]);

        $controller = new Usuario();

        $resultado = $controller->atualizar('uuid-123', ['nome' => 'Novo Nome'], $repositorio);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Novo Nome', $resultado['nome']);
    }

    public function testAuthenticate()
    {
        $repositorio = $this->createMock(RepositorioInterface::class);

        $usuario = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: password_hash('senha123', PASSWORD_BCRYPT),
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $authenticatedDto = new AuthenticatedDto(
            usuario: $usuario,
            token: 'token-123',
            tokenType: 'Bearer'
        );

        $authenticateUseCase = $this->createMock(AuthenticateUseCase::class);

        $authenticateUseCase->method('exec')
            ->willReturn($authenticatedDto);

        $this->instance(
            \App\Domain\Entity\Usuario\RepositorioInterface::class,
            $repositorio
        );

        $controller = new Usuario();

        $resultado = $controller->authenticate('joao@example.com', 'senha123', $authenticateUseCase);

        $this->assertInstanceOf(AuthenticatedDto::class, $resultado);
    }
}
