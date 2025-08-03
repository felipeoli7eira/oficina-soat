<?php

namespace Tests\Feature\Modules\Cliente;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use Database\Seeders\PapelSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioCadastroTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('roles');

        $this->seed(PapelSeed::class);
    }

    public function test_usuario_pode_ser_cadastrado_como_comercial(): void
    {
        $payload = [
            'nome'   => 'Comercial',
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
}
