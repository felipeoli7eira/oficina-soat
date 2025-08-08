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
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrdemServicoRemocaoTest extends TestCase
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

    public function test_ordem_de_servico_pode_ser_removida(): void
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

        $response = $this->withAuth()->postJson('/api/os', $payload);

        // Assert

        $response->assertCreated();

        // Act

        $deleteResponse = $this->withAuth()->delete('/api/os/' . $response->json('uuid'));
        $deleteResponse->assertNoContent();
    }

    public function test_ordem_de_servico_nao_pode_ser_removida_sem_um_uuid_valido(): void
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

        $response = $this->withAuth()->postJson('/api/os', $payload);

        // Assert

        $response->assertCreated();

        // Act

        $deleteResponse = $this->withAuth()->delete('/api/os/qualquer-coisa-nao-sendo-um-uuid-valido');
        $deleteResponse->assertBadRequest();
    }

    public function test_remocao_ordem_inexistente_lanca_model_not_found()
    {
        $uuidFake = 'inexistente-uuid-1234';

        // Mock do Service
        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('remocao')
            ->with($uuidFake)
            ->once()
            ->andThrow(new ModelNotFoundException());

        $controller = new OSController($mockService);

        // ✅ Mock COMPLETO do Request
        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\ObterUmPorUuidRequest::class);

        // Mocka TODOS os métodos que o Laravel pode chamar
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);
        $mockRequest->shouldReceive('all')->andReturn([]);
        $mockRequest->shouldReceive('route')->andReturn(null);
        $mockRequest->shouldReceive('input')->andReturn(null);
        $mockRequest->shouldReceive('get')->andReturn(null);

        // Propriedade pública para o uuid
        $mockRequest->uuid = $uuidFake;

        $response = $controller->remocao($mockRequest);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertStringContainsString('Nenhum registro correspondente ao informado', $response->getContent());
    }

    public function test_remocao_ordem_lanca_excecao_generica()
    {
        $uuidFake = 'uuid-com-erro-generico';

        // Mock do Service
        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('remocao')
            ->with($uuidFake)
            ->once()
            ->andThrow(new Exception('Erro'));

        $controller = new OSController($mockService);

        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldReceive('uuid')->andReturn($uuidFake);
        $mockRequest->shouldReceive('all')->andReturn([]);
        $mockRequest->shouldReceive('route')->andReturn(null);
        $mockRequest->shouldReceive('input')->andReturn(null);
        $mockRequest->shouldReceive('get')->andReturn(null);
        $mockRequest->uuid = $uuidFake;

        $response = $controller->remocao($mockRequest);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString('Erro', $response->getContent());
    }

    public function test_remocao_ordem_sucesso()
    {
        $uuidValido = 'uuid-valido-12345';
        $expectedResponse = ['message' => 'Removido com sucesso'];

        // Mock do Service
        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('remocao')
            ->with($uuidValido)
            ->once()
            ->andReturn($expectedResponse);

        $controller = new OSController($mockService);

        // Mock completo do Request
        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\ObterUmPorUuidRequest::class);
        $mockRequest->shouldReceive('uuid')->andReturn($uuidValido);
        $mockRequest->shouldReceive('all')->andReturn([]);
        $mockRequest->shouldReceive('route')->andReturn(null);
        $mockRequest->shouldReceive('input')->andReturn(null);
        $mockRequest->shouldReceive('get')->andReturn(null);
        $mockRequest->uuid = $uuidValido;

        $response = $controller->remocao($mockRequest);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($expectedResponse, $responseData);
    }
}
