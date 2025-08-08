<?php

namespace Tests\Feature\Modules\Usuario;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use App\Modules\Usuario\Model\Usuario;
use App\Modules\Usuario\Controller\Controller as UsuarioController;
use Database\Seeders\PapelSeed;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UsuarioRemocaoTest extends TestCase
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

    public function test_remocao_usuario_inexistente_lanca_model_not_found_exception()
    {
        $uuidFake = 'inexistente-uuid-1234';
;
        $this->service->shouldReceive('remocao')
            ->with($uuidFake)
            ->once()
            ->andThrow(new ModelNotFoundException());

        $mockRequest = Mockery::mock(\App\Modules\Usuario\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing();

        // Propriedade pública para o uuid
        $mockRequest->uuid = $uuidFake;

        $response = $this->controller->remocao($mockRequest);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_remocao_usuario_lanca_exception_generica()
    {
        $uuidFake = 'uuid-com-erro-generico';

        $this->service->shouldReceive('remocao')
            ->with($uuidFake)
            ->once()
            ->andThrow(new Exception('Erro'));

        $mockRequest = Mockery::mock(\App\Modules\Usuario\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->uuid = $uuidFake;
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);
        $mockRequest->shouldIgnoreMissing();

        $response = $this->controller->remocao($mockRequest);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
