<?php

namespace Tests\Unit;

use App\Modules\OrdemDeServicoServico\Dto\AtualizacaoDto;
use App\Modules\OrdemDeServicoServico\Dto\CadastroDto;
use App\Modules\OrdemDeServicoServico\Dto\ListagemDto;
use App\Modules\OrdemDeServicoServico\Model\OrdemDeServicoServico;
use App\Modules\OrdemDeServicoServico\Repository\Repository;
use App\Modules\OrdemDeServicoServico\Service\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdemDeServicoServicoTest extends TestCase
{
    use RefreshDatabase;

    // ==================== TESTES DOS DTOs ====================

    /**
     * Teste se o DTO de listagem retorna valores nulos inicialmente
     */
    public function test_listagem_dto_retorna_nulo_inicialmente(): void
    {
        $dto = new ListagemDto();
        $array = $dto->asArray();

        $this->assertIsArray($array);
        $this->assertEmpty($array);
    }

    /**
     * Teste se o DTO de listagem aceita parâmetros
     */
    public function test_listagem_dto_aceita_parametros(): void
    {
        // Como o DTO não tem parâmetros, apenas testamos que pode ser instanciado
        $dto = new ListagemDto();

        $this->assertInstanceOf(ListagemDto::class, $dto);
    }

    /**
     * Teste se o DTO de cadastro aceita dados do serviço da ordem de serviço
     */
    public function test_cadastro_dto_aceita_dados_servico_ordem_de_servico(): void
    {
        $dto = new CadastroDto(
            servico_uuid: 'servico-uuid-123',
            os_uuid: 'os-uuid-123',
            observacao: 'Observação do serviço',
            quantidade: 1,
            valor: 250.00,
            excluido: false,
            data_exclusao: null
        );

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEquals('servico-uuid-123', $result['servico_uuid']);
        $this->assertEquals('os-uuid-123', $result['os_uuid']);
        $this->assertEquals('Observação do serviço', $result['observacao']);
        $this->assertEquals(1, $result['quantidade']);
        $this->assertEquals(250.00, $result['valor']);
        $this->assertFalse($result['excluido']);
        $this->assertNull($result['data_exclusao']);
    }

    /**
     * Teste se o DTO de atualização retorna array vazio inicialmente
     */
    public function test_atualizacao_dto_retorna_array_vazio_inicialmente(): void
    {
        $dto = new AtualizacaoDto();
        $this->assertEquals([], $dto->asArray());
    }

    // ==================== TESTES DO MODEL ====================

    /**
     * Teste se o model OrdemDeServicoServico pode ser instanciado
     */
    public function test_ordem_de_servico_servico_model_pode_ser_instanciado(): void
    {
        $servico = new OrdemDeServicoServico();
        $this->assertInstanceOf(OrdemDeServicoServico::class, $servico);
    }

    /**
     * Teste se o model tem a tabela correta configurada
     */
    public function test_ordem_de_servico_servico_model_tem_tabela_correta(): void
    {
        $servico = new OrdemDeServicoServico();
        $this->assertEquals('os_servico', $servico->getTable());
    }

    /**
     * Teste se o model tem os campos fillable corretos
     */
    public function test_ordem_de_servico_servico_model_tem_fillable_corretos(): void
    {
        $servico = new OrdemDeServicoServico();

        $expectedFillable = [
            'servico_id',
            'os_id',
            'observacao',
            'quantidade',
            'valor',
            'excluido',
            'data_exclusao',
        ];

        $this->assertEquals($expectedFillable, $servico->getFillable());
    }

    // ==================== TESTES DO REPOSITORY ====================

    /**
     * Teste se o repository pode ser instanciado
     */
    public function test_ordem_de_servico_servico_repository_pode_ser_instanciado(): void
    {
        $repository = new Repository();
        $this->assertInstanceOf(Repository::class, $repository);
    }

    /**
     * Teste se o repository tem o model correto configurado
     */
    public function test_ordem_de_servico_servico_repository_tem_model_correto(): void
    {
        $model = Repository::model();
        $this->assertInstanceOf(OrdemDeServicoServico::class, $model);
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_ordem_de_servico_servico_service_pode_ser_instanciado(): void
    {
        $repository = $this->createMock('App\Modules\OrdemDeServicoServico\Repository\Repository');
        $servicoRepo = $this->createMock('App\Modules\Servico\Repository\ServicoRepository');
        $ordemDeServicoRepo = $this->createMock('App\Modules\OrdemDeServico\Repository\Repository');

        $service = new Service($repository, $servicoRepo, $ordemDeServicoRepo);
        $this->assertInstanceOf(Service::class, $service);
    }
}
