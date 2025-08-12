<?php

namespace Tests\Unit;

use App\Modules\OrdemDeServico\Dto\CadastroDto;
use App\Modules\OrdemDeServico\Dto\ListagemDto;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;
use App\Modules\OrdemDeServico\Repository\Repository;
use App\Modules\OrdemDeServico\Service\Service;
use App\Modules\Cliente\Repository\ClienteRepository;
use App\Modules\Usuario\Repository\UsuarioRepository;
use App\Modules\Veiculo\Repository\VeiculoRepository;
use Tests\TestCase;
use Mockery;

class OrdemDeServicoTest extends TestCase
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
            cliente_uuid: 'cliente-uuid-123',
            veiculo_uuid: 'veiculo-uuid-123',
            descricao: 'Teste de serviço',
            valor_desconto: 10.50,
            valor_total: 100.00,
            usuario_uuid_atendente: 'atendente-uuid-123',
            usuario_uuid_mecanico: 'mecanico-uuid-123',
            prazo_validade: 30
        );

        $this->assertEquals('cliente-uuid-123', $dto->cliente_uuid);
        $this->assertEquals('veiculo-uuid-123', $dto->veiculo_uuid);
        $this->assertEquals('Teste de serviço', $dto->descricao);
        $this->assertEquals(10.50, $dto->valor_desconto);
        $this->assertEquals(100.00, $dto->valor_total);
        $this->assertEquals('atendente-uuid-123', $dto->usuario_uuid_atendente);
        $this->assertEquals('mecanico-uuid-123', $dto->usuario_uuid_mecanico);
        $this->assertEquals(30, $dto->prazo_validade);

        $array = $dto->asArray();
        $this->assertIsArray($array);
        $this->assertEquals('cliente-uuid-123', $array['cliente_uuid']);
        $this->assertEquals('veiculo-uuid-123', $array['veiculo_uuid']);
        $this->assertEquals('Teste de serviço', $array['descricao']);
    }

    // ==================== TESTES DO MODEL ====================

    /**
     * Teste se o model pode ser instanciado
     */
    public function test_model_instanciacao(): void
    {
        $model = new OrdemDeServico();

        $this->assertInstanceOf(OrdemDeServico::class, $model);
        $this->assertEquals('os', $model->getTable());
    }

    /**
     * Teste se o model tem os fillable corretos
     */
    public function test_model_fillable(): void
    {
        $model = new OrdemDeServico();

        $expectedFillable = [
            'data_finalizacao',
            'prazo_validade',
            'cliente_id',
            'veiculo_id',
            'descricao',
            'valor_desconto',
            'valor_total',
            'status',
            'usuario_id_atendente',
            'usuario_id_mecanico',
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
        $repository = new Repository(new OrdemDeServico());

        $this->assertInstanceOf(Repository::class, $repository);
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_service_instanciacao(): void
    {
        $repository = Mockery::mock(Repository::class);
        $usuarioRepo = Mockery::mock(UsuarioRepository::class);
        $clienteRepo = Mockery::mock(ClienteRepository::class);
        $veiculoRepo = Mockery::mock(VeiculoRepository::class);

        $service = new Service(
            $repository,
            $usuarioRepo,
            $clienteRepo,
            $veiculoRepo
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
