<?php

namespace Tests\Feature\Modules\Usuario;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use App\Modules\Usuario\Model\Usuario;
use Database\Seeders\PapelSeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Usuario\Controller\Controller as UsuarioController;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UsuarioAtualizacaoTest extends TestCase
{
    use RefreshDatabase;

    public $controller;
    public $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('roles');

        $this->service = Mockery::mock('App\Modules\Usuario\Service\Service');
        $this->controller = new UsuarioController($this->service);

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

        $response = $this->withAuth()->putJson('/api/usuario/' . $usuario->uuid, [
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
            'email'    => 'NpH6g@example.com',
            'senha'    => 'senha',
            'status'   => StatusUsuario::ATIVO->value,
        ];

        $usuario = Usuario::factory()->create($payload)->assignRole(Papel::ATENDENTE->value)->fresh();

        // Act

        $papelDeMecanico = Role::findByName(Papel::MECANICO->value)->name;

        $response = $this->withAuth()->putJson('/api/usuario/' . $usuario->uuid, [
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

        $response = $this->withAuth()->putJson('/api/usuario/' . $usuario->uuid, [
            'status' => $desativado,
        ]);

        // Assert

        $response->assertOk();
    }

    public function test_atualizacao_usuario_nao_encontrado_lanca_model_not_found_exception(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockDto = Mockery::mock(\App\Modules\Usuario\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(\App\Modules\Usuario\Requests\AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->shouldReceive('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $this->service->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        // Act

        $response = $this->controller->atualizacao($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['error']);
    }

    public function test_atualizacao_usuario_lanca_exception_generica(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockDto = Mockery::mock(\App\Modules\Usuario\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(\App\Modules\Usuario\Requests\AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->shouldReceive('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $this->service->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow(Exception::class);

        // Act

        $response = $this->controller->atualizacao($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['error']);
    }
}
