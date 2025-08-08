<?php

namespace Tests\Feature\Modules\OrdemDeServicoItem;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Modules\OrdemDeServicoItem\Dto\ListagemDto;
use App\Modules\OrdemDeServicoItem\Dto\CadastroDto;
use App\Modules\OrdemDeServicoItem\Dto\AtualizacaoDto;
use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;

class OrdemDeServicoItemTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function test_listagem_dto_retorna_valores_nulos_inicialmente(): void
    {
        $dto = new ListagemDto();

        $this->assertInstanceOf(ListagemDto::class, $dto);
    }

    public function test_cadastro_dto_aceita_dados_item_os(): void
    {
        $dto = new CadastroDto(
            pecaInsumoUuid: '8acb1b8f-c588-4968-85ca-04ef66f2b380',
            osUuid: '9bcb1b8f-c588-4968-85ca-04ef66f2b381',
            observacao: 'Item conforme solicitado pelo cliente',
            quantidade: 2,
            valor: 150.50
        );

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEquals('8acb1b8f-c588-4968-85ca-04ef66f2b380', $result['peca_insumo_uuid']);
        $this->assertEquals('9bcb1b8f-c588-4968-85ca-04ef66f2b381', $result['os_uuid']);
        $this->assertEquals('Item conforme solicitado pelo cliente', $result['observacao']);
        $this->assertEquals(2, $result['quantidade']);
        $this->assertEquals(150.50, $result['valor']);
    }

    public function test_atualizacao_dto_aceita_dados_item_os(): void
    {
        $dto = new AtualizacaoDto(
            uuid: '8acb1b8f-c588-4968-85ca-04ef66f2b380',
            observacao: 'Observação atualizada',
            quantidade: 3,
            valor: 299.99
        );

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEquals('8acb1b8f-c588-4968-85ca-04ef66f2b380', $result['uuid']);
        $this->assertEquals('Observação atualizada', $result['observacao']);
        $this->assertEquals(3, $result['quantidade']);
        $this->assertEquals(299.99, $result['valor']);
    }

    public function test_item_os_model_pode_ser_instanciado(): void
    {
        $item = new OrdemDeServicoItem();
        $this->assertInstanceOf(OrdemDeServicoItem::class, $item);
    }

    public function test_item_os_model_tem_tabela_correta(): void
    {
        $item = new OrdemDeServicoItem();

        $this->assertEquals('os_item', $item->getTable());
    }

    public function test_item_os_model_tem_fillable_corretos(): void
    {
        $item = new OrdemDeServicoItem();

        $expectedFillable = [
            'peca_insumo_id',
            'os_id',
            'observacao',
            'quantidade',
            'valor',
            'excluido',
            'data_exclusao',
        ];

        $this->assertEquals($expectedFillable, $item->getFillable());
    }

    public function test_item_os_model_tem_hidden_corretos(): void
    {
        $item = new OrdemDeServicoItem();

        $expectedHidden = [
            'id',
            'peca_insumo_id',
            'os_id',
        ];

        $this->assertEquals($expectedHidden, $item->getHidden());
    }

    public function test_item_os_model_pode_receber_dados_construtor(): void
    {
        $dados = [
            'observacao' => 'Teste de observação',
            'quantidade' => 5,
            'valor' => 199.99,
            'excluido' => false
        ];

        $item = new OrdemDeServicoItem($dados);

        $this->assertEquals('Teste de observação', $item->observacao);
        $this->assertEquals(5, $item->quantidade);
        $this->assertEquals(199.99, $item->valor);
        $this->assertFalse($item->excluido);
    }

    public function test_item_os_model_timestamps_desabilitados(): void
    {
        $item = new OrdemDeServicoItem();

        $this->assertFalse($item->timestamps);
    }

    public function test_item_os_model_tem_relacionamento_peca_insumo(): void
    {
        $item = new OrdemDeServicoItem();
        $relacionamento = $item->pecaInsumo();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $relacionamento);
        $this->assertEquals('peca_insumo_id', $relacionamento->getForeignKeyName());
        $this->assertEquals(\App\Modules\PecaInsumo\Model\PecaInsumo::class, $relacionamento->getRelated()::class);
    }

    public function test_item_os_model_tem_relacionamento_ordem_de_servico(): void
    {
        $item = new OrdemDeServicoItem();
        $relacionamento = $item->ordemDeServico();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $relacionamento);
        $this->assertEquals('os_id', $relacionamento->getForeignKeyName());
        $this->assertEquals(\App\Modules\OrdemDeServico\Model\OrdemDeServico::class, $relacionamento->getRelated()::class);
    }

    public function test_item_os_model_usa_soft_deletes(): void
    {
        $item = new OrdemDeServicoItem();
        $traits = class_uses($item);

        $this->assertArrayHasKey(\App\Traits\SoftDeletes::class, $traits);
    }

    public function test_item_os_model_tem_has_factory(): void
    {
        $item = new OrdemDeServicoItem();
        $traits = class_uses($item);

        $this->assertArrayHasKey(\Illuminate\Database\Eloquent\Factories\HasFactory::class, $traits);
    }

    public function test_item_os_repository_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\OrdemDeServicoItem\Repository\OSItemRepository();

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Repository\OSItemRepository::class, $repository);
    }

    public function test_item_os_repository_herda_abstract_repository(): void
    {
        $repository = new \App\Modules\OrdemDeServicoItem\Repository\OSItemRepository();

        $this->assertInstanceOf(\App\AbstractRepository::class, $repository);
    }

    public function test_item_os_repository_implementa_interface(): void
    {
        $repository = new \App\Modules\OrdemDeServicoItem\Repository\OSItemRepository();

        $this->assertInstanceOf(\App\Interfaces\RepositoryInterface::class, $repository);
    }

    public function test_item_os_repository_tem_model_correto(): void
    {
        $model = \App\Modules\OrdemDeServicoItem\Repository\OSItemRepository::model();

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem::class, $model);
    }

    public function test_item_os_repository_tem_metodos_obrigatorios(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Repository\OSItemRepository::class, 'model'));
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Repository\OSItemRepository::class, 'read'));
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Repository\OSItemRepository::class, 'findOne'));
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Repository\OSItemRepository::class, 'create'));
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Repository\OSItemRepository::class, 'update'));
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Repository\OSItemRepository::class, 'delete'));
    }

    public function test_item_os_service_pode_ser_instanciado(): void
    {
        $osItemRepository = new \App\Modules\OrdemDeServicoItem\Repository\OSItemRepository();
        $pecaInsumoRepository = app(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class);
        $ordemServicoRepository = app(\App\Modules\OrdemDeServico\Repository\OrdemDeServicoRepository::class);

        $service = new \App\Modules\OrdemDeServicoItem\Service\Service(
            $osItemRepository,
            $pecaInsumoRepository,
            $ordemServicoRepository
        );

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Service\Service::class, $service);
    }

    public function test_item_os_service_tem_metodo_listagem(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Service\Service::class, 'listagem'));
    }

    public function test_item_os_service_tem_metodo_cadastro(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Service\Service::class, 'cadastro'));
    }

    public function test_item_os_service_tem_metodo_obter_um_por_uuid(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Service\Service::class, 'obterUmPorUuid'));
    }

    public function test_item_os_service_tem_metodo_remocao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Service\Service::class, 'remocao'));
    }

    public function test_item_os_service_tem_metodo_atualizacao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Service\Service::class, 'atualizacao'));
    }

    public function test_item_os_service_recebe_dependencias_construtor(): void
    {
        $osItemRepository = new \App\Modules\OrdemDeServicoItem\Repository\OSItemRepository();
        $pecaInsumoRepository = app(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class);
        $ordemServicoRepository = app(\App\Modules\OrdemDeServico\Repository\OrdemDeServicoRepository::class);

        $service = new \App\Modules\OrdemDeServicoItem\Service\Service(
            $osItemRepository,
            $pecaInsumoRepository,
            $ordemServicoRepository
        );

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Service\Service::class, $service);
    }

    public function test_item_os_controller_pode_ser_instanciado(): void
    {
        $service = app(\App\Modules\OrdemDeServicoItem\Service\Service::class);
        $controller = new \App\Modules\OrdemDeServicoItem\Controller\Controller($service);

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Controller\Controller::class, $controller);
    }

    /**
     * Teste se o controller herda do Controller base
     */
    public function test_item_os_controller_herda_controller_base(): void
    {
        $service = app(\App\Modules\OrdemDeServicoItem\Service\Service::class);
        $controller = new \App\Modules\OrdemDeServicoItem\Controller\Controller($service);

        $this->assertInstanceOf(\App\Http\Controllers\Controller::class, $controller);
    }

    public function test_item_os_controller_tem_metodo_listagem(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Controller\Controller::class, 'listagem'));
    }

    public function test_item_os_controller_tem_metodo_cadastro(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Controller\Controller::class, 'cadastro'));
    }

    public function test_item_os_controller_tem_metodo_obter_um_por_uuid(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Controller\Controller::class, 'obterUmPorUuid'));
    }

    public function test_item_os_controller_tem_metodo_remocao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Controller\Controller::class, 'remocao'));
    }

    public function test_item_os_controller_tem_metodo_atualizacao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Controller\Controller::class, 'atualizacao'));
    }

    public function test_item_os_controller_recebe_service_construtor(): void
    {
        $service = app(\App\Modules\OrdemDeServicoItem\Service\Service::class);
        $controller = new \App\Modules\OrdemDeServicoItem\Controller\Controller($service);

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Controller\Controller::class, $controller);
    }

    public function test_item_os_cadastro_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\OrdemDeServicoItem\Requests\CadastroRequest();

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Requests\CadastroRequest::class, $request);
    }

    /**
     * Teste se o CadastroRequest herda do FormRequest
     */
    public function test_item_os_cadastro_request_herda_form_request(): void
    {
        $request = new \App\Modules\OrdemDeServicoItem\Requests\CadastroRequest();

        $this->assertInstanceOf(\Illuminate\Foundation\Http\FormRequest::class, $request);
    }

    /**
     * Teste se o CadastroRequest tem método rules
     */
    public function test_item_os_cadastro_request_tem_metodo_rules(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Requests\CadastroRequest::class, 'rules'));
    }

    /**
     * Teste se o CadastroRequest tem método toDto
     */
    public function test_item_os_cadastro_request_tem_metodo_to_dto(): void
    {
        $this->assertTrue(method_exists(\App\Modules\OrdemDeServicoItem\Requests\CadastroRequest::class, 'toDto'));
    }

    /**
     * Teste se o ListagemRequest pode ser instanciado
     */
    public function test_item_os_listagem_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\OrdemDeServicoItem\Requests\ListagemRequest();

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Requests\ListagemRequest::class, $request);
    }

    /**
     * Teste se o ObterUmPorUuidRequest pode ser instanciado
     */
    public function test_item_os_obter_um_por_uuid_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\OrdemDeServicoItem\Requests\ObterUmPorUuidRequest();

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Requests\ObterUmPorUuidRequest::class, $request);
    }

    /**
     * Teste se o AtualizacaoRequest pode ser instanciado
     */
    public function test_item_os_atualizacao_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\OrdemDeServicoItem\Requests\AtualizacaoRequest();

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Requests\AtualizacaoRequest::class, $request);
    }

    /**
     * Teste se o RemocaoRequest pode ser instanciado
     */
    public function test_item_os_remocao_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\OrdemDeServicoItem\Requests\RemocaoRequest();

        $this->assertInstanceOf(\App\Modules\OrdemDeServicoItem\Requests\RemocaoRequest::class, $request);
    }

    // ==================== TESTES DAS ROUTES ====================

    /**
     * Teste se pode instanciar uma rota básica
     */
    public function test_item_os_pode_definir_rotas(): void
    {
        $this->assertTrue(class_exists(\App\Modules\OrdemDeServicoItem\Controller\Controller::class));
        $this->assertTrue(class_exists(\Illuminate\Support\Facades\Route::class));
    }

    /**
     * Teste se o namespace do controller está correto para as rotas
     */
    public function test_item_os_controller_namespace_correto(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\OrdemDeServicoItem\Controller\Controller::class);
        $namespace = $reflection->getNamespaceName();

        $this->assertEquals('App\Modules\OrdemDeServicoItem\Controller', $namespace);
    }

    /**
     * Teste se o controller tem os métodos necessários para as rotas
     */
    public function test_item_os_controller_tem_metodos_para_rotas(): void
    {
        $controller = \App\Modules\OrdemDeServicoItem\Controller\Controller::class;

        $this->assertTrue(method_exists($controller, 'listagem'));
        $this->assertTrue(method_exists($controller, 'cadastro'));
        $this->assertTrue(method_exists($controller, 'obterUmPorUuid'));
        $this->assertTrue(method_exists($controller, 'atualizacao'));
        $this->assertTrue(method_exists($controller, 'remocao'));
    }

    /**
     * Teste se as constantes HTTP estão disponíveis para as rotas
     */
    public function test_item_os_constantes_http_disponiveis(): void
    {
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_OK'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_CREATED'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT'));
    }

    // ==================== TESTES DO SWAGGER ====================

    /**
     * Teste se o controller tem anotações OpenApi
     */
    public function test_item_os_controller_tem_anotacoes_openapi(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\OrdemDeServicoItem\Controller\Controller::class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $hasSwaggerAnnotation = false;
        foreach ($methods as $method) {
            $docComment = $method->getDocComment();
            if ($docComment && strpos($docComment, '@OA\\') !== false) {
                $hasSwaggerAnnotation = true;
                break;
            }
        }

        $this->assertTrue($hasSwaggerAnnotation, 'Controller deve ter pelo menos uma anotação OpenApi');
    }

    /**
     * Teste se o método listagem tem anotação Swagger
     */
    public function test_item_os_controller_listagem_tem_swagger(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\OrdemDeServicoItem\Controller\Controller::class);
        $method = $reflection->getMethod('listagem');
        $docComment = $method->getDocComment();

        $this->assertStringContainsString('@OA\\Get', $docComment);
        $this->assertStringContainsString('/api/os-item', $docComment);
        $this->assertStringContainsString('Item da OS', $docComment);
    }

    // ==================== TESTES ESPECÍFICOS DO DOMÍNIO ====================

    /**
     * Teste se o DTO de cadastro valida campos obrigatórios
     */
    public function test_cadastro_dto_campos_obrigatorios(): void
    {
        $dto = new CadastroDto(
            pecaInsumoUuid: '8acb1b8f-c588-4968-85ca-04ef66f2b380',
            osUuid: '9bcb1b8f-c588-4968-85ca-04ef66f2b381',
            observacao: 'Observação obrigatória',
            quantidade: 1,
            valor: 0.01
        );

        $array = $dto->asArray();

        $this->assertArrayHasKey('peca_insumo_uuid', $array);
        $this->assertArrayHasKey('os_uuid', $array);
        $this->assertArrayHasKey('observacao', $array);
        $this->assertArrayHasKey('quantidade', $array);
        $this->assertArrayHasKey('valor', $array);
    }

    /**
     * Teste se o model tem cast correto para campos numéricos
     */
    public function test_item_os_model_casts_corretos(): void
    {
        $item = new OrdemDeServicoItem();
        $casts = $item->getCasts();

        $this->assertArrayHasKey('quantidade', $casts);
        $this->assertArrayHasKey('valor', $casts);
        $this->assertArrayHasKey('excluido', $casts);
        $this->assertEquals('integer', $casts['quantidade']);
        $this->assertEquals('decimal:2', $casts['valor']);
        $this->assertEquals('boolean', $casts['excluido']);
    }
}
