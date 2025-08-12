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

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('cliente');
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
}
