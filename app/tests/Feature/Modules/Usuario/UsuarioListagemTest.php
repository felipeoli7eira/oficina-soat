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
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UsuarioListagemTest extends TestCase
{
    use RefreshDatabase;

    private $controller;
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('roles');

        $this->service = Mockery::mock('App\Modules\Usuario\Service\Service');
        $this->controller = new UsuarioController($this->service);

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

    public function test_listagem_de_usuario_lanca_exception(): void
    {
        // Arrange

        $this->service
            ->shouldReceive('listagem')
            ->once()
            ->andThrow(Exception::class);

        // Act

        $response = $this->controller->listagem();

        // Assert

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_metodo_obter_um_por_uuid_no_controller_de_usuario_recupera_not_found_exception(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(\App\Modules\Usuario\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->uuid = $uuidFake;

        $modelNotFound = new ModelNotFoundException();
        $modelNotFound->setModel(\App\Modules\Usuario\Model\Usuario::class);

        $this->service->shouldReceive('obterUmPorUuid')
            ->with($uuidFake)
            ->once()
            ->andThrow($modelNotFound);

        // Act

        $response = $this->controller->obterUmPorUuid($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
    }

    public function test_metodo_obter_um_por_uuid_no_controller_de_usuario_recupera_exception_generica(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(\App\Modules\Usuario\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->uuid = $uuidFake;

        $modelNotFound = new ModelNotFoundException();
        $modelNotFound->setModel(\App\Modules\Usuario\Model\Usuario::class);

        $this->service->shouldReceive('obterUmPorUuid')
            ->with($uuidFake)
            ->once()
            ->andThrow(Exception::class);

        // Act

        $response = $this->controller->obterUmPorUuid($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
    }
}
