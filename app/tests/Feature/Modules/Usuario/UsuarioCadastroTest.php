<?php

namespace Tests\Feature\Modules\Usuario;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use Database\Seeders\PapelSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Usuario\Controller\Controller as UsuarioController;
use App\Modules\Usuario\Dto\CadastroDto;
use App\Modules\Usuario\Requests\CadastroRequest;
use Exception;
use Mockery;
use Tests\TestCase;

class UsuarioCadastroTest extends TestCase
{
    use RefreshDatabase;

    private $service;
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('roles');

        $this->service = Mockery::mock('App\Modules\Usuario\Service\Service');
        $this->controller = new UsuarioController($this->service);

        $this->seed(PapelSeed::class);
    }

    public function test_usuario_pode_ser_cadastrado_como_comercial(): void
    {
        $payload = [
            'nome'   => 'Comercial',
            'email'  => 'comercial@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::COMERCIAL->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertCreated();
        $this->assertDatabaseCount('usuario', 2); // 1 do withAuth() + 1 cadastrado
    }

    public function test_usuario_pode_ser_cadastrado_como_mecanico(): void
    {
        $payload = [
            'nome'   => 'Mecânico',
            'email'  => 'mecanico@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::MECANICO->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertCreated();
        $this->assertDatabaseCount('usuario', 2); // 1 do withAuth() + 1 cadastrado
    }

    public function test_usuario_pode_ser_cadastrado_como_atendente(): void
    {
        $payload = [
            'nome'   => 'Atendente',
            'email'  => 'atendente@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::ATENDENTE->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertCreated();
        $this->assertDatabaseCount('usuario', 2); // 1 do withAuth() + 1 cadastrado
    }

    public function test_usuario_pode_ser_cadastrado_como_gestor_de_estoque(): void
    {
        $payload = [
            'nome'   => 'Gestor de estoque',
            'email'  => 'gestor@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::GESTOR_ESTOQUE->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertCreated();
        $this->assertDatabaseCount('usuario', 2); // 1 do withAuth() + 1 cadastrado
    }

    public function test_usuario_atendente_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'email'  => 'semNome@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::ATENDENTE->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_mecanico_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'email'  => 'semNome2@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::MECANICO->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_comercial_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'email'  => 'semNome3@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::COMERCIAL->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_gestor_estoque_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'email'  => 'semNome4@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::GESTOR_ESTOQUE->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_nao_pode_ser_cadastrado_sem_dados_obrigatorios(): void
    {
        $payload = [];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_cadastro_de_usuario_lanca_exception(): void
    {
        // Arrange

        $mockDto = Mockery::mock(CadastroDto::class);
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);
        $mockRequest->shouldIgnoreMissing();

        $this->service
            ->shouldReceive('cadastro')
            ->with($mockDto)
            ->once()
            ->andThrow(Exception::class);

        // Act

        $response = $this->controller->cadastro($mockRequest);

        // Assert

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_usuario_nao_pode_ser_cadastrado_com_email_duplicado(): void
    {
        // Primeiro cadastro
        $payload = [
            'nome'   => 'Primeiro Usuario',
            'email'  => 'duplicado@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::MECANICO->value,
        ];

        $this->withAuth()->postJson('/api/usuario', $payload);

        // Segundo cadastro com mesmo email
        $payload2 = [
            'nome'   => 'Segundo Usuario',
            'email'  => 'duplicado@example.com', // Email duplicado
            'senha'  => 'outrasenha123',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::ATENDENTE->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload2);

        $response->assertBadRequest();
    }

    public function test_usuario_nao_pode_ser_cadastrado_com_senha_muito_curta(): void
    {
        $payload = [
            'nome'   => 'Usuario Senha Curta',
            'email'  => 'senhacurta@example.com',
            'senha'  => '123', // Muito curta
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::MECANICO->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_nao_pode_ser_cadastrado_com_papel_invalido(): void
    {
        $payload = [
            'nome'   => 'Usuario Papel Invalido',
            'email'  => 'papelinvalido@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => 'papel_inexistente', // Papel inválido
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_cadastro_usuario_com_erro_interno(): void
    {
        $this->mock(\App\Modules\Usuario\Service\Service::class, function ($mock) {
            $mock->shouldReceive('cadastro')
                ->once()
                ->andThrow(new \Exception('Erro interno simulado no cadastro'));
        });

        $payload = [
            'nome'   => 'Usuario Erro Interno',
            'email'  => 'errointerno@example.com',
            'senha'  => 'senha8caracteres',
            'status' => 'ativo', // Usar valor em minúsculo
            'papel'  => Papel::MECANICO->value,
        ];

        $response = $this->withAuth()->postJson('/api/usuario', $payload);

        $response->assertStatus(500); // Erro interno deve retornar 500
    }
}
