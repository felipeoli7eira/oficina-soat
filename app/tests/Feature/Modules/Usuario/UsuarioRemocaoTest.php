<?php

namespace Tests\Feature\Modules\Cliente;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use App\Modules\Usuario\Model\Usuario;
use Database\Seeders\PapelSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsuarioRemocaoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('roles');

        $this->seed(PapelSeed::class);
    }

    public function test_usuario_pode_ser_removido(): void
    {
        // Arrange

        $payload = [
            'nome'    => 'Atendente',
            'status'  => StatusUsuario::ATIVO->value,
        ];

        $usuario = Usuario::factory()->create($payload)->fresh();

        // Act

        $response = $this->delete('/api/usuario/' . $usuario->uuid);

        // Assert

        $response->assertNoContent();
    }
}
