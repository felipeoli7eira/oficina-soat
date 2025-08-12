<?php

namespace Tests\Unit;

use App\Modules\Cliente\Service\Service as ClienteService;
use App\Modules\ClienteVeiculo\Dto\CadastroDto;
use App\Modules\ClienteVeiculo\Model\ClienteVeiculo;
use App\Modules\ClienteVeiculo\Repository\Repository as ClienteVeiculoRepository;
use App\Modules\ClienteVeiculo\Service\Service as ClienteVeiculoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteVeiculoTest extends TestCase
{
    use RefreshDatabase;

    // ==================== TESTES DOS DTOs ====================

    /**
     * Teste se o DTO de cadastro aceita dados do cliente e veículo
     */
    public function test_cadastro_dto_aceita_dados_cliente_veiculo(): void
    {
        $dto = new CadastroDto(
            veiculoId: 1,
            clienteId: 2
        );

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEquals(2, $result['cliente_id']);
        $this->assertEquals(1, $result['veiculo_id']);
    }

    // ==================== TESTES DO MODEL ====================

    /**
     * Teste se o model ClienteVeiculo pode ser instanciado
     */
    public function test_cliente_veiculo_model_pode_ser_instanciado(): void
    {
        $cv = new ClienteVeiculo();
        $this->assertInstanceOf(ClienteVeiculo::class, $cv);
    }

    /**
     * Teste se o model tem a tabela correta configurada
     */
    public function test_cliente_veiculo_model_tem_tabela_correta(): void
    {
        $cv = new ClienteVeiculo();
        $this->assertEquals('cliente_veiculo', $cv->getTable());
    }

    /**
     * Teste se o model tem os campos fillable corretos
     */
    public function test_cliente_veiculo_model_tem_fillable_corretos(): void
    {
        $cv = new ClienteVeiculo();

        $expectedFillable = [
            'cliente_id',
            'veiculo_id',
        ];

        $this->assertEquals($expectedFillable, $cv->getFillable());
    }

    // ==================== TESTES DO REPOSITORY ====================

    /**
     * Teste se o repository pode ser instanciado
     */
    public function test_cliente_veiculo_repository_pode_ser_instanciado(): void
    {
        $repository = new ClienteVeiculoRepository();
        $this->assertInstanceOf(ClienteVeiculoRepository::class, $repository);
    }

    /**
     * Teste se o repository tem o model correto configurado
     */
    public function test_cliente_veiculo_repository_tem_model_correto(): void
    {
        $model = ClienteVeiculoRepository::model();
        $this->assertInstanceOf(ClienteVeiculo::class, $model);
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_cliente_veiculo_service_pode_ser_instanciado(): void
    {
        $repository = $this->createMock(ClienteVeiculoRepository::class);
        $clienteService = $this->createMock(ClienteService::class);
        $service = new ClienteVeiculoService($repository, $clienteService);
        $this->assertInstanceOf(ClienteVeiculoService::class, $service);
    }
}
