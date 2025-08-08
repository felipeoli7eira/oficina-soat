<?php

namespace Tests\Feature\Modules\OS;

use App\Enums\Papel;
use App\Modules\Cliente\Model\Cliente;
use App\Modules\OrdemDeServico\Requests\CadastroRequest;

use App\Modules\OrdemDeServico\Service\Service as OSService;

use App\Modules\OrdemDeServico\Controller\Controller;

use App\Modules\Usuario\Model\Usuario;
use App\Modules\Veiculo\Model\Veiculo;

use Database\Seeders\DatabaseSeeder;
use DomainException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrdemServicoCadastroTest extends TestCase
{
    use RefreshDatabase;

    private $serviceMock;
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(OSService::class);
        $this->controller = new Controller($this->serviceMock);

        $this->assertDatabaseEmpty('os');
        $this->assertDatabaseEmpty('veiculo');
        $this->assertDatabaseEmpty('cliente');
        $this->assertDatabaseEmpty('usuario');
        $this->assertDatabaseEmpty('model_has_roles');

        $this->seed(DatabaseSeeder::class);
    }

    public function test_ordem_de_servico_pode_ser_cadastrada(): void
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
    }

    public function test_ordem_de_servico_nao_pode_ser_cadastrada_quando_um_atendente_eh_informado_incorretamente(): void
    {
        // Arrange

        $cliente = Cliente::factory()->create()->fresh();
        $veiculo = Veiculo::factory()->create()->fresh();

        $atendente = Usuario::factory()->create()->fresh();
        $atendente->assignRole(Papel::COMERCIAL->value); // erro proposital de regra de negocio aqui

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

        $response->assertBadRequest();
    }

    public function test_ordem_de_servico_nao_pode_ser_cadastrada_quando_um_mecanico_eh_informado_incorretamente(): void
    {
        // Arrange

        $cliente = Cliente::factory()->create()->fresh();
        $veiculo = Veiculo::factory()->create()->fresh();

        $atendente = Usuario::factory()->create()->fresh();
        $atendente->assignRole(Papel::ATENDENTE->value);

        $mecanico = Usuario::factory()->create()->fresh();
        $mecanico->assignRole(Papel::COMERCIAL->value); // erro proposital de regra de negocio aqui

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

        $response->assertBadRequest();
    }

    public function test_ordem_de_servico_nao_pode_ser_cadastrada_sem_um_ou_mais_dados_obrigatorios(): void
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
            // 'descricao'              => '', simulando o nao envio dessa chave
            'valor_total'            => 1000,
            'valor_desconto'         => 50,
            'prazo_validade'         => 7,
        ];

        // Act

        $response = $this->postJson('/api/os', $payload);

        // Assert

        $response->assertBadRequest();
    }

    public function test_cadastro_de_os_retorna_erro_de_regra_de_negocio(): void
    {
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $dtoFake = Mockery::mock(\App\Modules\OrdemDeServico\Dto\CadastroDto::class);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($dtoFake);

        $domainException = new DomainException('Erro de regra de negócio', 400);

        $this->serviceMock->shouldReceive('cadastro')
            ->with($dtoFake)
            ->once()
            ->andThrow($domainException);

        $response = $this->controller->cadastro($mockRequest);

        $this->assertEquals(400, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['error']);
    }

    public function test_cadastro_de_os_retorna_erro_interno_em_excecao_generica(): void
    {
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $dtoFake = Mockery::mock(\App\Modules\OrdemDeServico\Dto\CadastroDto::class);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($dtoFake);

        $erroGenerico = new Exception('Erro');

        $this->serviceMock->shouldReceive('cadastro')
            ->with($dtoFake)
            ->once()
            ->andThrow($erroGenerico);

        $response = $this->controller->cadastro($mockRequest);

        $this->assertEquals(500, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertEquals('Erro', $data['message']);
    }
}
