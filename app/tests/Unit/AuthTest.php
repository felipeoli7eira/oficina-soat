<?php

namespace Tests\Unit;

use App\Modules\Auth\Services\AuthUsuarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o AuthUsuarioService pode ser instanciado
     */
    public function test_auth_usuario_service_pode_ser_instanciado(): void
    {
        $service = new AuthUsuarioService();
        $this->assertInstanceOf(AuthUsuarioService::class, $service);
    }

    /**
     * Teste se o método de autenticar com email e senha do AuthUsuarioService funciona
     */
    public function test_auth_usuario_service_autenticar_funciona(): void
    {
        $credentials = ['email' => 'test@example.com', 'password' => 'password'];

        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with($credentials)
            ->andReturn('fake-token');

        JWTAuth::shouldReceive('factory->getTTL')
            ->andReturn(60);

        $service = new AuthUsuarioService();

        $response = $service->autenticarComEmailESenha($credentials['email'], $credentials['password']);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('access_token', $response);
        $this->assertEquals('fake-token', $response['access_token']);
    }
}
