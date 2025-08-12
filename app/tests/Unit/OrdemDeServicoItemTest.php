<?php

namespace Tests\Unit;

use App\Modules\OrdemDeServicoItem\Dto\CadastroDto;
use App\Modules\OrdemDeServicoItem\Dto\ListagemDto;
use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;
use App\Modules\OrdemDeServicoItem\Repository\Repository;
use App\Modules\OrdemDeServicoItem\Service\Service;
use Tests\TestCase;
use Mockery;

class OrdemDeServicoItemTest extends TestCase
{
    // ==================== TESTES DOS DTOs ====================

    /**
     * Teste se o DTO de listagem funciona corretamente
     */
    public function test_listagem_dto(): void
    {
        $dto = new ListagemDto();
        $array = $dto->asArray();

        $this->assertIsArray($array);
        $this->assertEmpty($array);
    }

    /**
     * Teste se o DTO de cadastro funciona corretamente
     */
    public function test_cadastro_dto(): void
    {
        $dto = new CadastroDto(
            peca_insumo_uuid: 'peca-uuid-123',
            os_uuid: 'os-uuid-123',
            observacao: 'Observação do item',
            quantidade: 2,
            valor: 50.00,
            excluido: false,
            data_exclusao: null
        );

        $this->assertEquals('peca-uuid-123', $dto->peca_insumo_uuid);
        $this->assertEquals('os-uuid-123', $dto->os_uuid);
        $this->assertEquals('Observação do item', $dto->observacao);
        $this->assertEquals(2, $dto->quantidade);
        $this->assertEquals(50.00, $dto->valor);
        $this->assertFalse($dto->excluido);
        $this->assertNull($dto->data_exclusao);

        $array = $dto->asArray();
        $this->assertIsArray($array);
        $this->assertEquals('peca-uuid-123', $array['peca_insumo_uuid']);
        $this->assertEquals('os-uuid-123', $array['os_uuid']);
        $this->assertEquals('Observação do item', $array['observacao']);
        $this->assertEquals(2, $array['quantidade']);
        $this->assertEquals(50.00, $array['valor']);
    }

    // ==================== TESTES DO MODEL ====================

    /**
     * Teste se o model pode ser instanciado
     */
    public function test_model_instanciacao(): void
    {
        $model = new OrdemDeServicoItem();

        $this->assertInstanceOf(OrdemDeServicoItem::class, $model);
        $this->assertEquals('os_item', $model->getTable());
    }

    /**
     * Teste se o model tem os fillable corretos
     */
    public function test_model_fillable(): void
    {
        $model = new OrdemDeServicoItem();

        $expectedFillable = [
            'peca_insumo_id',
            'os_id',
            'observacao',
            'quantidade',
            'valor',
            'excluido',
            'data_exclusao',
        ];

        $this->assertEquals($expectedFillable, $model->getFillable());
    }

    // ==================== TESTES DO REPOSITORY ====================

    /**
     * Teste se o repository pode ser instanciado
     */
    public function test_repository_instanciacao(): void
    {
        $repository = new Repository(new OrdemDeServicoItem());

        $this->assertInstanceOf(Repository::class, $repository);
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_service_instanciacao(): void
    {
        $repository = Mockery::mock(Repository::class);
        $pecaInsumoRepo = Mockery::mock('App\Modules\PecaInsumo\Repository\PecaInsumoRepository');
        $ordemDeServicoRepo = Mockery::mock('App\Modules\OrdemDeServico\Repository\Repository');

        $service = new Service(
            $repository,
            $pecaInsumoRepo,
            $ordemDeServicoRepo
        );

        $this->assertInstanceOf(Service::class, $service);
    }

    /**
     * Teardown para limpar mocks
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
