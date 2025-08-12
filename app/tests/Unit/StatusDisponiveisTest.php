<?php

namespace Tests\Unit;

use App\Modules\StatusDisponiveis\Dto\CadastroDto;
use App\Modules\StatusDisponiveis\Dto\ListagemDto;
use App\Modules\StatusDisponiveis\Model\StatusDisponiveis;
use App\Modules\StatusDisponiveis\Repository\StatusDisponiveisRepository;
use App\Modules\StatusDisponiveis\Service\Service;
use Tests\TestCase;
use Mockery;

class StatusDisponiveisTest extends TestCase
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
            descricao: 'Status Aberto',
            ordem: 1
        );

        $this->assertEquals('Status Aberto', $dto->descricao);
        $this->assertEquals(1, $dto->ordem);

        $array = $dto->asArray();
        $this->assertIsArray($array);
        $this->assertEquals('Status Aberto', $array['descricao']);
        $this->assertEquals(1, $array['ordem']);
    }

    // ==================== TESTES DO MODEL ====================

    /**
     * Teste se o model pode ser instanciado
     */
    public function test_model_instanciacao(): void
    {
        $model = new StatusDisponiveis();

        $this->assertInstanceOf(StatusDisponiveis::class, $model);
        $this->assertEquals('status_disponiveis', $model->getTable());
    }

    /**
     * Teste se o model tem os fillable corretos
     */
    public function test_model_fillable(): void
    {
        $model = new StatusDisponiveis();

        $expectedFillable = [
            'descricao',
            'ordem',
        ];

        $this->assertEquals($expectedFillable, $model->getFillable());
    }

    // ==================== TESTES DO REPOSITORY ====================

    /**
     * Teste se o repository pode ser instanciado
     */
    public function test_repository_instanciacao(): void
    {
        $repository = new StatusDisponiveisRepository(new StatusDisponiveis());

        $this->assertInstanceOf(StatusDisponiveisRepository::class, $repository);
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_service_instanciacao(): void
    {
        $repository = Mockery::mock(StatusDisponiveisRepository::class);

        $service = new Service($repository);

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
