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

    public function test_usuario_pode_ter_o_nome_atualizado(): void
    {
        // Arrange

        $payload = [
            'nome'     => 'Atendente',
            'status'   => StatusUsuario::ATIVO->value,
        ];

        $usuario = Usuario::factory()->create($payload)->assignRole(Papel::ATENDENTE->value)->fresh();

        // Act

        $response = $this->putJson('/api/usuario/' . $usuario->uuid, [
            'nome' => 'novo',
        ]);

        // Assert

        $response->assertOk();
        $this->assertDatabaseHas('usuario', [
            'uuid' => $usuario->uuid,
            'nome' => 'novo',
        ]);
    }

    public function test_usuario_pode_ter_papel_atualizado(): void
    {
        // Arrange

        // cadastro como atendente (por exemplo)
        $payload = [
            'nome'     => 'Atendente',
            'status'   => StatusUsuario::ATIVO->value,
        ];

        $usuario = Usuario::factory()->create($payload)->assignRole(Papel::ATENDENTE->value)->fresh();

        // Act

        $papelDeMecanico = Role::findByName(Papel::MECANICO->value)->name;

        $response = $this->putJson('/api/usuario/' . $usuario->uuid, [
            'papel' => $papelDeMecanico,
        ]);

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
        ];

        $usuario = Usuario::factory()->create($payload)->assignRole(Papel::ATENDENTE->value)->fresh();

        // Act

        $desativado = StatusUsuario::INATIVO->value;

        $response = $this->putJson('/api/usuario/' . $usuario->uuid, [
            'status' => $desativado,
        ]);

        // Assert

        $response->assertOk();
    }
}
