<?php

namespace Tests\Feature\Modules\Cliente;

use App\Modules\Cliente\Requests\ObterUmPorUuidRequest;
use App\Modules\Cliente\Requests\ListagemRequest;

use App\Modules\Cliente\Service\Service as ClienteService;
use App\Modules\Cliente\Controller\ClienteController;

use App\Modules\Cliente\Model\Cliente;

use Database\Seeders\DatabaseSeeder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ClienteListagemTest extends TestCase
{
    use RefreshDatabase;

    private $controller;
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('cliente');

        $this->service = Mockery::mock(ClienteService::class);
        $this->controller = new ClienteController($this->service);
    }

    public function test_listar_todos_clientes(): void
    {
        $clientes = \App\Modules\Cliente\Model\Cliente::factory(3)->create();
        $response = $this->withAuth()->getJson('/api/cliente');

        $response->assertOk();
    }

    public function test_listar_clientes_com_erro_interno(): void
    {
        $this->mock(\App\Modules\Cliente\Service\Service::class, function ($mock) {
            $mock->shouldReceive('listagem')
                ->once()
                ->andThrow(new \Exception('Erro interno na listagem'));
        });

        $response = $this->withAuth()->getJson('/api/cliente');

        $response->assertStatus(500);
    }

    public function test_metodo_obterUmPorUuid_no_controller_retorna_um_cliente_corretamente(): void
    {
        // Arrange

        $cliente = Cliente::factory()->createOne()->fresh();

        // Act

        $response = $this->withAuth()->getJson('/api/cliente/' . $cliente->uuid);

        // Assert

        $response->assertOk();
    }

    public function test_metodo_obterUmPorUuid_no_controller_captura_erro_interno_no_servidor(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockRequest = Mockery::mock(\App\Modules\Cliente\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->uuid = $uuidFake;

        $modelNotFound = new ModelNotFoundException();
        $modelNotFound->setModel(\App\Modules\Cliente\Model\Cliente::class);

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
