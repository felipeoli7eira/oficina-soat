<?php

namespace Tests\Feature\Modules\Cliente;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use App\Modules\Usuario\Model\Usuario;
use Database\Seeders\PapelSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsuarioAtualizacaoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('roles');

        $this->seed(PapelSeed::class);
    }

    // a
    // c
    // g
    // m

    public function test_usuario_atendente_pode_ter_o_nome_atualizado(): void
    {
        // Arrange

        $payload = [
            'nome'     => 'Atendente',
            'status'   => StatusUsuario::ATIVO->value,
            'role_id'  => Role::findByName(Papel::ATENDENTE->value)->id
        ];

        $usuario = Usuario::factory()->create($payload)->fresh();

        unset($payload['role_id']);

        $payload['papel'] = 'atendente';

        // Act

        $novoNome = 'nome atualizado';

        $payload['nome'] = $novoNome;

        $response = $this->putJson('/api/usuario/' . $usuario->uuid, $payload);

        // Assert

        $response->assertOk();
        $this->assertDatabaseHas('usuario', [
            'uuid' => $usuario->uuid,
            'nome' => $novoNome,
        ]);
    }

    public function test_usuario_pode_ter_papel_atualizado(): void
    {
        // Arrange

        // cadastro como atendente (por exemplo)
        $payload = [
            'nome'     => 'Atendente',
            'status'   => StatusUsuario::ATIVO->value,
            'role_id'  => Role::findByName(Papel::ATENDENTE->value)->id
        ];

        $usuario = Usuario::factory()->create($payload)->fresh();

        // limpa role_id por que o endpoint de atualiacao recebe como uma abtracao pela chave "papel" e resolve o id depois
        unset($payload['role_id']);

        // Act

        $papelDeMecanico = Role::findByName(Papel::MECANICO->value)->name;
        $payload['papel'] = $papelDeMecanico;

        $response = $this->putJson('/api/usuario/' . $usuario->uuid, $payload);

        // Assert

        $response->assertOk();
    }

    public function test_usuario_pode_ter_status_atualizado(): void
    {
        // Arrange

        // cadastro como atendente (por exemplo)
        $payload = [
            'nome'     => 'Atendente',
            'status'   => StatusUsuario::ATIVO->value,
            'role_id'  => Role::findByName(Papel::ATENDENTE->value)->id
        ];

        $usuario = Usuario::factory()->create($payload)->fresh();

        // limpa role_id por que o endpoint de atualiacao recebe como uma abtracao pela chave "papel" e resolve o id depois
        unset($payload['role_id']);

        // Act

        $desativado = StatusUsuario::INATIVO->value;
        $payload['status'] = $desativado;

        $response = $this->putJson('/api/usuario/' . $usuario->uuid, $payload);

        // Assert

        $response->assertOk();
    }
}
