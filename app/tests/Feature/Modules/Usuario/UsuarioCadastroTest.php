<?php

namespace Tests\Feature\Modules\Cliente;

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
            'status' => 'ativo',
            'papel'  => 'comercial'
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertCreated();
    }

    public function test_usuario_pode_ser_cadastrado_como_mecanico(): void
    {
        $payload = [
            'nome'   => 'Mecânico',
            'status' => 'ativo',
            'papel'  => 'mecanico'
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertCreated();
    }

    public function test_usuario_pode_ser_cadastrado_como_atendente(): void
    {
        $payload = [
            'nome'   => 'Atendente',
            'status' => 'ativo',
            'papel'  => 'atendente'
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertCreated();
    }

    public function test_usuario_pode_ser_cadastrado_como_gestor_de_estoque(): void
    {
        $payload = [
            'nome'   => 'Gestor de estoque',
            'status' => 'ativo',
            'papel'  => 'gestor_estoque'
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertCreated();
    }

    public function test_usuario_atendente_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'status' => 'ativo',
            'papel'  => 'atendente'
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_mecanico_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'status' => 'ativo',
            'papel'  => 'mecanico'
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_comercial_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'status' => 'ativo',
            'papel'  => 'comercial'
        ];

        $response = $this->postJson('/api/usuario', $payload);

        $response->assertBadRequest();
    }

    public function test_usuario_gestor_estoque_nao_pode_ser_cadastrado_sem_um_nome(): void
    {
        $payload = [
            'status' => 'ativo',
            'papel'  => 'gestor_estoque'
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
