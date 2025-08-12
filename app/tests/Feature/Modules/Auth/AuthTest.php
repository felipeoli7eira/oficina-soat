<?php

namespace Tests\Feature\Modules\Auth;

use App\Modules\Usuario\Model\Usuario;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
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

    public function test_retorna_dados_do_usuario_logado_pelo_jwt(){
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

    // public function test_logout_usuario()
    // {
    //     Usuario::factory()->create([
    //         'email' => 'usuario@teste.com',
    //         'senha' => Hash::make('senha8caracteres'),
    //     ]);

    //     $payload = [
    //         'email' => 'usuario@teste.com',
    //         'senha' => 'senha8caracteres',
    //     ];

    //     $response = $this->postJson('/api/usuario/auth/autenticar', $payload);
    //     $response->assertOk();
    //     $token = $response['access_token'];

    //     $response = $this->withToken($token)
    //                      ->postJson('/api/usuario/auth/logout');

    //     $response->assertOk();
    //     $response->assertJsonStructure([
    //          'message'
    //     ]);
    // }
}