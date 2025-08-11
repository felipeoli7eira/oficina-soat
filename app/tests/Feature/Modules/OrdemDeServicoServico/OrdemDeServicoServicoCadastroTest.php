<?php

namespace Tests\Feature\Modules\OrdemDeServicoServico;

use App\Modules\OrdemDeServicoServico\Requests\CadastroRequest;

use App\Modules\OrdemDeServicoServico\Service\Service as OSServicoService;

use App\Modules\OrdemDeServicoServico\Controller\Controller;

use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use App\Modules\Servico\Model\Servico;
use App\Modules\OrdemDeServicoServico\Model\OrdemDeServicoServico;

use Database\Seeders\DatabaseSeeder;
use DomainException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrdemDeServicoServicoCadastroTest extends TestCase
{
    use RefreshDatabase;

    private $serviceMock;
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(OSServicoService::class);
        $this->controller = new Controller($this->serviceMock);

        $this->assertDatabaseEmpty('os');
        $this->assertDatabaseEmpty('os_servico');
        $this->assertDatabaseEmpty('servicos');

        $this->seed(DatabaseSeeder::class);
    }

    public function test_ordem_de_servico_servico_pode_ser_cadastrada(): void
    {
        $os = OrdemDeServico::factory()->create()->fresh();
        $servico = Servico::factory()->create()->fresh();

        $payload = [
            'os_uuid'           => $os->uuid,
            'servico_uuid'      => $servico->uuid,
            'observacao'        => 'Serviço conforme solicitado pelo cliente',
            'quantidade'        => 1,
            'valor'             => 1000,
        ];

        $response = $this->withAuth()->postJson('/api/os-servico', $payload);

        $response->assertCreated();
    }

    public function test_ordem_de_servico_servico_nao_pode_ser_cadastrada_sem_um_ou_mais_dados_obrigatorios(): void
    {
        // Arrange
        $os = OrdemDeServico::factory()->create()->fresh();
        $servico = Servico::factory()->create()->fresh();

        $payload = [
            'os_uuid'           => $os->uuid,
            'servico_uuid'      => $servico->uuid,
            'observacao'        => 'Serviço conforme solicitado pelo cliente',
            'quantidade'        => 1,
            // 'valor'             => 1000,
        ];

        // Act
        $response = $this->withAuth()->postJson('/api/os-servico', $payload);

        // Assert
        $response->assertBadRequest();
    }

    public function test_cadastro_de_os_servico_retorna_erro_de_regra_de_negocio(): void
    {
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $dtoFake = Mockery::mock(\App\Modules\OrdemDeServicoServico\Dto\CadastroDto::class);
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

    public function test_cadastro_de_os_servico_retorna_erro_interno_em_excecao_generica(): void
    {
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $dtoFake = Mockery::mock(\App\Modules\OrdemDeServicoServico\Dto\CadastroDto::class);
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
