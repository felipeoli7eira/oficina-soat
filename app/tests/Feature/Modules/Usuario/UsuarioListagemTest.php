<?php

namespace Tests\Feature\Modules\Cliente;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use App\Modules\Usuario\Model\Usuario;
use Database\Seeders\PapelSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioListagemTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('roles');

        $this->seed(PapelSeed::class);
    }

    public function test_usuarios_cadastrados_podem_ser_listados(): void
    {
        // Arrange

        Usuario::factory(3)->create();

        // Act

        $response = $this->getJson('/api/usuario');

        // Assert

        $response->assertOk();
    }

    public function test_usuario_pode_ser_listado_informando_uuid(): void
    {
        // Arrange

        $payload = [
            'nome'    => fake()->name(),
            'status'  => StatusUsuario::ATIVO->value,
        ];

        $usuario = Usuario::factory()->create($payload)->fresh();

        // Act

        $response = $this->getJson('/api/usuario/' . $usuario->uuid);

        // Assert

        $response->assertOk();
    }
}
