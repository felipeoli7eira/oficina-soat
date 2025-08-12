<?php

namespace Tests\Feature\Modules\Auth;

use App\Modules\Usuario\Model\Usuario;
use App\Modules\Auth\Services\AuthUsuarioService;
use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\Controllers\AuthUsuarioController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private $controller;
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('roles');

        $this->service = Mockery::mock(\App\Modules\Auth\Services\AuthUsuarioService::class);
        $this->controller = new AuthUsuarioController($this->service);
    }


    public function test_login_com_usuario_valido()
    {
        $usuario = Usuario::factory()->create([
            'email' => 'usuario@teste.com',
            'senha' => Hash::make('senha8caracteres'),
        ]);

        $payload = [
            'email' => 'usuario@teste.com',
            'senha' => 'senha8caracteres',
        ];

        $response = $this->postJson('/api/usuario/auth/autenticar', $payload);
        $response->assertOk();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function test_autenticar_com_credenciais_invalidas_deve_lancar_domain_exception()
    {
        // Arrange
        $service = new \App\Modules\Auth\Services\AuthUsuarioService();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Credenciais inválidas');

        // Act
        $service->autenticarComEmailESenha('email@invalido.com', 'senhaerrada');
    }

    public function test_login_com_email_do_usuario_nao_existente()
    {
        Usuario::factory()->create([
            'email' => 'usuario@teste.com',
            'senha' => Hash::make('senha8caracteres'),
        ]);

        $payload = [
            'email' => 'usuario2@teste.com',
            'senha' => 'senha8caracteres',
        ];

        $response = $this->postJson('/api/usuario/auth/autenticar', $payload);

        $response->assertBadRequest();
    }

    public function test_retorna_dados_do_usuario_logado_pelo_jwt()
    {
        $usuario = Usuario::factory()->create([
            'email' => 'usuario@teste.com',
            'senha' => Hash::make('senha8caracteres'),
        ]);

        $payload = [
            'email' => 'usuario@teste.com',
            'senha' => 'senha8caracteres',
        ];

        $response = $this->postJson('/api/usuario/auth/autenticar', $payload);
        $response->assertOk();
        $token = $response['access_token'];

        $response = $this->withToken($token)
            ->getJson('/api/usuario/auth/identidade');

        $response->assertOk();
        $response->assertJson([
            'email' => 'usuario@teste.com',
        ]);
    }

    public function test_autenticacao_com_credenciais_invalidas_retorna_401(): void
    {
        // Arrange
        $payload = [
            'email' => 'usuario@teste.com',
            'senha' => 'senhaerrada',
        ];

        // Garante que exista um usuário, mas senha não bate
        \App\Modules\Usuario\Model\Usuario::factory()->create([
            'email' => 'usuario@teste.com',
            'senha' => \Illuminate\Support\Facades\Hash::make('outra_senha'),
        ]);

        // Act
        $response = $this->postJson('/api/usuario/auth/autenticar', $payload);

        // Assert
        $response->assertStatus(401);
        $data = $response->getData(true);
        $this->assertTrue($data['error']);
    }

    public function test_logout_de_usuario_autenticado_acontece()
    {
        // Arrange

        $usuario = \App\Modules\Usuario\Model\Usuario::factory()->create([
            'email' => 'usuario@teste.com',
            'senha' => \Illuminate\Support\Facades\Hash::make('senha8caracteres'),
        ]);

        $token = auth()->attempt([
            'email'    => $usuario->email,
            'password' => 'senha8caracteres',
        ]);

        // Act

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/usuario/auth/logout');

        // Assert

        $response->assertOk();
        $response->assertJson([
            'message' => 'Successfully logged out'
        ]);
    }
}
