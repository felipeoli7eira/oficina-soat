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
            'email'  => 'NpH6g@example.com',
            'senha'  => 'senha8caracteres',
            'status' => StatusUsuario::ATIVO->value,
            'papel'  => Papel::COMERCIAL->value,
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertCreated();
    }

    public function test_usuario_pode_ser_cadastrado_como_mecanico(): void
    {
        $payload = [
            'nome'   => 'Mecânico',
            'email'  => 'NpH6g@example.com',
            'senha'  => 'senha8caracteres',
            'status' => StatusUsuario::ATIVO->value,
            'papel'  => Papel::MECANICO->value,
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertCreated();
    }

    public function test_usuario_pode_ser_cadastrado_como_atendente(): void
    {
        $payload = [
            'nome'   => 'Atendente',
            'email'  => 'NpH6g@example.com',
            'senha'  => 'senha8caracteres',
            'status' => StatusUsuario::ATIVO->value,
            'papel'  => Papel::ATENDENTE->value,
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertCreated();
    }

    public function test_usuario_pode_ser_cadastrado_como_gestor_de_estoque(): void
    {
        $payload = [
            'nome'   => 'Gestor de estoque',
            'email'  => 'NpH6g@example.com',
            'senha'  => 'senha8caracteres',
            'status' => StatusUsuario::ATIVO->value,
            'papel'  => Papel::GESTOR_ESTOQUE->value,
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertCreated();
    }

    public function test_usuario_atendente_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'status' => StatusUsuario::ATIVO->value,
            'papel'  => Papel::ATENDENTE->value,
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_mecanico_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'status' => StatusUsuario::ATIVO->value,
            'papel'  => Papel::MECANICO->value,
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_comercial_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'status' => StatusUsuario::ATIVO->value,
            'papel'  => Papel::COMERCIAL->value,
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_gestor_estoque_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'status' => StatusUsuario::ATIVO->value,
            'papel'  => Papel::GESTOR_ESTOQUE->value,
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_nao_pode_ser_cadastrado_sem_dados_obrigatorios(): void
    {
        $payload = [];

        $response = $this->postJson('/api/usuario', $payload);

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
}
