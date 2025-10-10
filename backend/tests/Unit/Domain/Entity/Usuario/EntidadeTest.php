<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\Perfil;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    public function testCriarEntidadeComDadosValidos()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: password_hash('senha123', PASSWORD_BCRYPT),
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('João Silva', $entidade->nome);
        $this->assertEquals('joao@example.com', $entidade->email);
        $this->assertTrue($entidade->ativo);
        $this->assertEquals(Perfil::ATENDENTE->value, $entidade->perfil);
    }

    public function testValidarEmailInvalido()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email inválido');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'email-invalido',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarNomeComMenosDe3Caracteres()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'AB',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarNomeVazio()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        new Entidade(
            uuid: 'uuid-123',
            nome: '   ',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarPerfilInvalido()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Perfil inválido');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: 'perfil_inexistente',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testAtivarUsuario()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: false,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->ativar();

        $this->assertTrue($entidade->ativo);
    }

    public function testDesativarUsuario()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->desativar();

        $this->assertFalse($entidade->ativo);
    }

    public function testExcluirUsuario()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->excluir();

        $this->assertNotNull($entidade->deletadoEm);
        $this->assertFalse($entidade->ativo);
    }

    public function testEstaExcluidoRetornaTrue()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->excluir();

        $this->assertTrue($entidade->estaExcluido());
    }

    public function testEstaExcluidoRetornaFalse()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertFalse($entidade->estaExcluido());
    }

    public function testToHttpResponse()
    {
        $criadoEm = new DateTimeImmutable();
        $atualizadoEm = new DateTimeImmutable();

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: $criadoEm,
            atualizadoEm: $atualizadoEm
        );

        $response = $entidade->toHttpResponse();

        $this->assertIsArray($response);
        $this->assertEquals('uuid-123', $response['uuid']);
        $this->assertEquals('João Silva', $response['nome']);
        $this->assertEquals('joao@example.com', $response['email']);
        $this->assertTrue($response['ativo']);
        $this->assertEquals(Perfil::ATENDENTE->value, $response['perfil']);
        $this->assertEquals($criadoEm, $response['criado_em']);
        $this->assertEquals($atualizadoEm, $response['atualizado_em']);
        $this->assertNull($response['deletado_em']);
    }

    public function testToCreateDataArray()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $data = $entidade->toCreateDataArray();

        $this->assertIsArray($data);
        $this->assertEquals('João Silva', $data['nome']);
        $this->assertEquals('joao@example.com', $data['email']);
        $this->assertEquals('senha123', $data['senha']);
        $this->assertEquals(Perfil::ATENDENTE->value, $data['perfil']);
    }

    public function testToTokenPayload()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: 'senha123',
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $payload = $entidade->toTokenPayload();

        $this->assertIsArray($payload);
        $this->assertEquals('uuid-123', $payload['sub']);
        $this->assertEquals(Perfil::ATENDENTE->value, $payload['perf']);
    }

    public function testVerifyPasswordComSenhaCorreta()
    {
        $senha = 'senha123';
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: password_hash($senha, PASSWORD_BCRYPT),
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertTrue($entidade->verifyPassword($senha));
    }

    public function testVerifyPasswordComSenhaIncorreta()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            email: 'joao@example.com',
            senha: password_hash('senha123', PASSWORD_BCRYPT),
            ativo: true,
            perfil: Perfil::ATENDENTE->value,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertFalse($entidade->verifyPassword('senha_errada'));
    }

    public function testTodosOsPerfisValidos()
    {
        $perfis = [
            Perfil::ATENDENTE->value,
            Perfil::COMERCIAL->value,
            Perfil::MECANICO->value,
            Perfil::GESTOR_ESTOQUE->value,
        ];

        foreach ($perfis as $perfil) {
            $entidade = new Entidade(
                uuid: 'uuid-123',
                nome: 'João Silva',
                email: 'joao@example.com',
                senha: 'senha123',
                ativo: true,
                perfil: $perfil,
                criadoEm: new DateTimeImmutable(),
                atualizadoEm: new DateTimeImmutable()
            );

            $this->assertEquals($perfil, $entidade->perfil);
        }
    }
}
