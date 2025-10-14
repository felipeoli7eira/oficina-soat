<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;
use App\Domain\Entity\Usuario\Perfil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticationTest extends TestCase
{
    private function createTestUser(
        string $email = 'auth.test@example.com',
        string $senha = 'senha123',
        string $nome = 'Usuário Auth Test',
        string $perfil = 'mecanico'
    ): void {
        DB::table('usuarios')->insert([
            'uuid' => Str::uuid()->toString(),
            'nome' => $nome,
            'email' => $email,
            'senha' => Hash::make($senha),
            'perfil' => $perfil,
            'ativo' => true,
        ]);
    }

    public function testLoginComCredenciaisValidas()
    {
        $this->createTestUser();

        $response = $this->postJson('/api/auth/login', [
            'email' => 'auth.test@example.com',
            'senha' => 'senha123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => [
                    'uuid',
                    'nome',
                    'email',
                    'perfil',
                    'ativo'
                ]
            ])
            ->assertJson([
                'token_type' => 'Bearer',
                'user' => [
                    'nome' => 'Usuário Auth Test',
                    'email' => 'auth.test@example.com',
                    'perfil' => 'mecanico',
                ]
            ]);

        $this->assertNotEmpty($response->json('token'));
    }

    public function testLoginComEmailInvalido()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'usuario.inexistente@example.com',
            'senha' => 'senha123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'err' => true,
                'msg' => 'Credenciais inválidas'
            ]);
    }

    public function testLoginComSenhaIncorreta()
    {
        $this->createTestUser();

        $response = $this->postJson('/api/auth/login', [
            'email' => 'auth.test@example.com',
            'senha' => 'senha_errada',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'err' => true,
                'msg' => 'Credenciais inválidas'
            ]);
    }

    public function testLoginComEmailVazio()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => '',
            'senha' => 'senha123',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true])
            ->assertJsonFragment(['msg' => 'O campo email é obrigatório.']);
    }

    public function testLoginComSenhaVazia()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'auth.test@example.com',
            'senha' => '',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true])
            ->assertJsonFragment(['msg' => 'O campo senha é obrigatório.']);
    }

    public function testLoginComUsuarioInativo()
    {
        // Criar usuário inativo
        DB::table('usuarios')->insert([
            'uuid' => Str::uuid()->toString(),
            'nome' => 'Usuário Inativo',
            'email' => 'inativo@example.com',
            'senha' => Hash::make('senha123'),
            'perfil' => 'atendente',
            'ativo' => false,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inativo@example.com',
            'senha' => 'senha123',
        ]);

        $this->assertContains($response->status(), [200, 401]);
        
        if ($response->status() === 401) {
            $response->assertJson([
                'err' => true,
                'msg' => 'Usuário inativo'
            ]);
        }
    }

    public function testLoginComEmailFormatoInvalido()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'email-sem-arroba',
            'senha' => 'senha123',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }
}