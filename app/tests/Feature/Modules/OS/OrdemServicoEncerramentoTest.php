<?php

namespace Tests\Feature\Modules\OS;

use App\Enums\Papel;
use App\Modules\Cliente\Model\Cliente;
use App\Modules\Usuario\Model\Usuario;
use App\Modules\Veiculo\Model\Veiculo;

use App\Modules\OrdemDeServico\Controller\Controller as OSController;
use App\Modules\OrdemDeServico\Service\Service as OSService;

use Database\Seeders\DatabaseSeeder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Event\Code\Throwable;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrdemServicoEncerramentoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('os');
        $this->assertDatabaseEmpty('veiculo');
        $this->assertDatabaseEmpty('cliente');
        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('model_has_roles');

        $this->seed(DatabaseSeeder::class);
    }

    public function test_ordem_de_servico_pode_ser_encerrada(): void
    {
        // Arrange

        $cliente = Cliente::factory()->create()->fresh();
        $veiculo = Veiculo::factory()->create()->fresh();

        $atendente = Usuario::factory()->create()->fresh();
        $atendente->assignRole(Papel::ATENDENTE->value);

        $mecanico = Usuario::factory()->create()->fresh();
        $mecanico->assignRole(Papel::MECANICO->value);

        $payload = [
            'cliente_uuid'           => $cliente->uuid,
            'veiculo_uuid'           => $veiculo->uuid,
            'usuario_uuid_atendente' => $atendente->uuid,
            'usuario_uuid_mecanico'  => $mecanico->uuid,
            'descricao'              => 'Motor batendo em baixa rotação',
            'valor_total'            => 1000,
            'valor_desconto'         => 50,
            'prazo_validade'         => 7,
        ];

        // Act

        $response = $this->postJson('/api/os', $payload);

        // Assert

        $response->assertCreated();

        // Act

        $putResponse = $this->put('/api/os/encerrar/' . $response->json('uuid'));
        $putResponse->assertOk();
    }

    public function test_metodo_encerrar_no_controller_de_os_retorna_exception_generica()
    {
        // Arrange

        $uuidFake = 'uuid-fake-erro-generico';

        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('encerrar')
            ->with($uuidFake)
            ->once()
            ->andThrow(new Exception('Erro inesperado'));

        $controller = new OSController($mockService);

        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing(); // evita erros com all(), authorize() etc.
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);

        // Act

        $response = $controller->encerrar($mockRequest);

        // Asserts

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString('Erro inesperado', $response->getContent());
    }

    public function test_metodo_encerrar_no_controller_de_os_lanca_model_not_found_exception()
    {
        $uuidFake = 'uuid-nao-existe';

        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('encerrar')
            ->with($uuidFake)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora chamadas como all(), authorize(), etc.
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);

        $controller = new OSController($mockService);

        // Act
        $response = $controller->encerrar($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertTrue($responseData['error']);
        $this->assertEquals('Nenhum registro correspondente ao informado', $responseData['message']);
    }
}
