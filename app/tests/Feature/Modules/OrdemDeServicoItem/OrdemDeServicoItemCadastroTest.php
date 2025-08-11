<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Modules\OrdemDeServicoItem\Requests\CadastroRequest;

use App\Modules\OrdemDeServicoItem\Service\Service as OSItemService;

use App\Modules\OrdemDeServicoItem\Controller\Controller;

use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;

use Database\Seeders\DatabaseSeeder;
use DomainException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrdemDeServicoItemCadastroTest extends TestCase
{
    use RefreshDatabase;

    private $serviceMock;
    private $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceMock = Mockery::mock(OSItemService::class);
        $this->controller = new Controller($this->serviceMock);

        $this->assertDatabaseEmpty('os');
        $this->assertDatabaseEmpty('os_item');
        $this->assertDatabaseEmpty('peca_insumo');

        $this->seed(DatabaseSeeder::class);
    }

    public function test_ordem_de_servico_item_pode_ser_cadastrada(): void
    {
        $os = OrdemDeServico::factory()->create()->fresh();
        $pecainsumo = PecaInsumo::factory()->create()->fresh();


        $payload = [
            'os_uuid'           => $os->uuid,
            'peca_insumo_uuid'  => $pecainsumo->uuid,
            'observacao'        => 'Item conforme solicitado pelo cliente',
            'quantidade'        => 1,
            'valor'             => 1000,
        ];

        $response = $this->postJson('/api/os-item', $payload);

        $response->assertCreated();
    }

    public function test_ordem_de_servico_item_nao_pode_ser_cadastrada_sem_um_ou_mais_dados_obrigatorios(): void
    {
        // Arrange
        $os = OrdemDeServico::factory()->create()->fresh();
        $pecainsumo = PecaInsumo::factory()->create()->fresh();

        $payload = [
            'os_uuid'           => $os->uuid,
            'peca_insumo_uuid'  => $pecainsumo->uuid,
            'observacao'        => 'Item conforme solicitado pelo cliente',
            'quantidade'        => 1,
            // 'valor'             => 1000,
        ];

        // Act

        $response = $this->postJson('/api/os-item', $payload);

        // Assert

        $response->assertBadRequest();
    }

    public function test_cadastro_de_os_item_retorna_erro_de_regra_de_negocio(): void
    {
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $dtoFake = Mockery::mock(\App\Modules\OrdemDeServicoItem\Dto\CadastroDto::class);
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

    public function test_cadastro_de_os_item_retorna_erro_interno_em_excecao_generica(): void
    {
        $mockRequest = Mockery::mock(CadastroRequest::class);
        $mockRequest->shouldIgnoreMissing();

        $dtoFake = Mockery::mock(\App\Modules\OrdemDeServicoItem\Dto\CadastroDto::class);
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
