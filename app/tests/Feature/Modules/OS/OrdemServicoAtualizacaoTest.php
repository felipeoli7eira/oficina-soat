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

class OrdemServicoAtualizacaoTest extends TestCase
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

    public function test_ordem_de_servico_pode_ser_atualizada(): void
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

        // Assert

        $updateResponse = $this->putJson('/api/os/' . $response->json('uuid'), [
            'descricao' => 'Motor batendo em alta rotação',
        ]);

        $updateResponse->assertOk();
    }

    public function test_veiculo_na_ordem_de_servico_pode_ser_corrigido(): void
    {
        // Arrange

        $cliente = Cliente::factory()->create()->fresh();
        $veiculo = Veiculo::factory()->create()->fresh();

        $veiculoParaUpdate = Veiculo::factory()->create()->fresh();

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

        // Assert

        $updateResponse = $this->putJson('/api/os/' . $response->json('uuid'), [
            'veiculo_uuid' => $veiculoParaUpdate->uuid,
        ]);

        $updateResponse->assertOk();
    }

    public function test_cliente_na_ordem_de_servico_pode_ser_corrigido(): void
    {
        // Arrange

        $cliente = Cliente::factory()->create()->fresh();
        $veiculo = Veiculo::factory()->create()->fresh();

        $clienteParaUpdate = Cliente::factory()->create()->fresh();

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

        // Assert

        $updateResponse = $this->putJson('/api/os/' . $response->json('uuid'), [
            'cliente_uuid' => $clienteParaUpdate->uuid,
        ]);

        $updateResponse->assertOk();
    }

    public function test_atendente_na_ordem_de_servico_pode_ser_corrigido(): void
    {
        // Arrange

        $cliente = Cliente::factory()->create()->fresh();
        $veiculo = Veiculo::factory()->create()->fresh();

        $atendente = Usuario::factory()->create()->fresh();
        $atendente->assignRole(Papel::ATENDENTE->value);

        $atendenteParaUpdate = Usuario::factory()->create()->fresh();
        $atendenteParaUpdate->assignRole(Papel::ATENDENTE->value);

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

        // Assert

        $updateResponse = $this->putJson('/api/os/' . $response->json('uuid'), [
            'usuario_uuid_atendente' => $atendenteParaUpdate->uuid,
        ]);

        $updateResponse->assertOk();
    }

    public function test_mecanico_na_ordem_de_servico_pode_ser_corrigido(): void
    {
        // Arrange

        $cliente = Cliente::factory()->create()->fresh();
        $veiculo = Veiculo::factory()->create()->fresh();

        $atendente = Usuario::factory()->create()->fresh();
        $atendente->assignRole(Papel::ATENDENTE->value);

        $mecanico = Usuario::factory()->create()->fresh();
        $mecanico->assignRole(Papel::MECANICO->value);

        $mecanicoParaUpdate = Usuario::factory()->create()->fresh();
        $mecanicoParaUpdate->assignRole(Papel::MECANICO->value);

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

        // Assert

        $updateResponse = $this->putJson('/api/os/' . $response->json('uuid'), [
            'usuario_uuid_mecanico' => $mecanicoParaUpdate->uuid,
        ]);

        $updateResponse->assertOk();
    }

    public function test_usuario_nao_mecanico_nao_pode_ser_associado_como_mecanico_da_os(): void
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

        // Assert

        $updateResponse = $this->putJson('/api/os/' . $response->json('uuid'), [
            'usuario_uuid_mecanico' => $atendente->uuid,
        ]);

        $updateResponse->assertBadRequest();
    }

    public function test_usuario_nao_atendente_nao_pode_ser_associado_como_atendente_da_os(): void
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

        // Assert

        $updateResponse = $this->putJson('/api/os/' . $response->json('uuid'), [
            'usuario_uuid_atendente' => $mecanico->uuid,
        ]);

        $updateResponse->assertBadRequest();
    }

    public function test_atualizacao_os_nao_encontrada_lanca_model_not_found_exception(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockDto = Mockery::mock(\App\Modules\OrdemDeServico\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->shouldReceive('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        $controller = new OSController($mockService);

        // Act

        $response = $controller->atualizacao($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['error']);
        $this->assertEquals('Nenhum registro correspondente ao informado', $responseData['message']);
    }

    public function test_atualizacao_os_lanca_exception_generica(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockDto = Mockery::mock(\App\Modules\OrdemDeServico\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(\App\Modules\OrdemDeServico\Requests\AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->shouldReceive('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $mockService = Mockery::mock(OSService::class);
        $mockService->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow(Exception::class);

        $controller = new OSController($mockService);

        // Act

        $response = $controller->atualizacao($mockRequest);

        // Assert

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['error']);
    }
}
