<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use App\Enums\Papel;
use App\Modules\OrdemDeServicoItem\Requests\AtualizacaoRequest;

use App\Modules\OrdemDeServicoItem\Service\Service as OSItemService;

use App\Modules\OrdemDeServicoItem\Controller\Controller;

use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;

use Database\Seeders\DatabaseSeeder;
use DomainException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrdemServicoItemAtualizacaoTest extends TestCase
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

    public function test_ordem_de_servico_item_pode_ser_atualizada(): void
    {
        // Arrange
        $os = OrdemDeServico::factory()->create()->fresh();
        $pecainsumo = PecaInsumo::factory()->create()->fresh();

        $payload = [
            'os_uuid'           => $os->uuid,
            'peca_insumo_uuid'  => $pecainsumo->uuid,
            'observacao'        => 'Item conforme solicitado pelo cliente',
            'quantidade'        => 1,
            'valor'             => 1000,
        ];

        // Act - Criar o item primeiro
        $response = $this->withAuth()->postJson('/api/os-item', $payload);
        $response->assertCreated();

        // Act - Atualizar o item
        $updateResponse = $this->withAuth()->putJson('/api/os-item/' . $response->json('uuid'), [
            'observacao' => 'Item atualizado conforme nova solicitação',
        ]);

        // Assert
        $updateResponse->assertOk();
    }

    public function test_quantidade_do_item_pode_ser_atualizada(): void
    {
        // Arrange
        $os = OrdemDeServico::factory()->create()->fresh();
        $pecainsumo = PecaInsumo::factory()->create()->fresh();

        $payload = [
            'os_uuid'           => $os->uuid,
            'peca_insumo_uuid'  => $pecainsumo->uuid,
            'observacao'        => 'Item conforme solicitado pelo cliente',
            'quantidade'        => 1,
            'valor'             => 1000,
        ];

        // Act - Criar o item primeiro
        $response = $this->withAuth()->postJson('/api/os-item', $payload);
        $response->assertCreated();

        // Act - Atualizar quantidade
        $updateResponse = $this->withAuth()->putJson('/api/os-item/' . $response->json('uuid'), [
            'quantidade' => 5,
        ]);

        // Assert
        $updateResponse->assertOk();
    }

    public function test_valor_do_item_pode_ser_atualizado(): void
    {
        // Arrange
        $os = OrdemDeServico::factory()->create()->fresh();
        $pecainsumo = PecaInsumo::factory()->create()->fresh();

        $payload = [
            'os_uuid'           => $os->uuid,
            'peca_insumo_uuid'  => $pecainsumo->uuid,
            'observacao'        => 'Item conforme solicitado pelo cliente',
            'quantidade'        => 1,
            'valor'             => 1000,
        ];

        // Act - Criar o item primeiro
        $response = $this->withAuth()->postJson('/api/os-item', $payload);
        $response->assertCreated();

        // Act - Atualizar valor
        $updateResponse = $this->withAuth()->putJson('/api/os-item/' . $response->json('uuid'), [
            'valor' => 1500,
        ]);

        // Assert
        $updateResponse->assertOk();
    }

    public function test_atualizacao_os_item_nao_encontrado_lanca_model_not_found_exception(): void
    {
        $uuidFake = 'uuid-inexistente-1234';

        $mockDto = Mockery::mock(\App\Modules\OrdemDeServicoItem\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing(); // ignora outros métodos que não forem stubados
        $mockRequest->shouldReceive('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $this->serviceMock->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        // Act
        $response = $this->controller->atualizacao($mockRequest);

        // Assert
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['error']);
        $this->assertEquals('Nenhum registro correspondente ao informado', $responseData['message']);
    }

    public function test_atualizacao_os_item_retorna_erro_de_regra_de_negocio(): void
    {
        $uuidFake = 'uuid-valido-1234';

        $mockDto = Mockery::mock(\App\Modules\OrdemDeServicoItem\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $domainException = new DomainException('Erro de regra de negócio', 400);

        $this->serviceMock->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow($domainException);

        // Act
        $response = $this->controller->atualizacao($mockRequest);

        // Assert
        $this->assertEquals(400, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['error']);
    }

    public function test_atualizacao_os_item_retorna_erro_interno_em_excecao_generica(): void
    {
        $uuidFake = 'uuid-valido-1234';

        $mockDto = Mockery::mock(\App\Modules\OrdemDeServicoItem\Dto\AtualizacaoDto::class);

        $mockRequest = Mockery::mock(AtualizacaoRequest::class);
        $mockRequest->shouldIgnoreMissing();
        $mockRequest->shouldReceive('uuid')->once()->andReturn($uuidFake);
        $mockRequest->shouldReceive('toDto')->once()->andReturn($mockDto);

        $erroGenerico = new Exception('Erro interno');

        $this->serviceMock->shouldReceive('atualizacao')
            ->with($uuidFake, $mockDto)
            ->once()
            ->andThrow($erroGenerico);

        // Act
        $response = $this->controller->atualizacao($mockRequest);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['error']);
        $this->assertEquals('Erro interno', $data['message']);
    }
}
