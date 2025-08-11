<?php

namespace Tests\Feature\Modules\PecaInsumo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Modules\PecaInsumo\Dto\ListagemDto;
use App\Modules\PecaInsumo\Dto\CadastroDto;
use App\Modules\PecaInsumo\Model\PecaInsumo;

class PecaInsumoTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;


    // ==================== TESTES DOS DTOs ====================

    /**
     * Teste se o DTO de listagem retorna array vazio inicialmente
     */
    public function test_listagem_dto_retorna_array_vazio_inicialmente(): void
    {
        $dto = new ListagemDto();

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Teste se o DTO de cadastro aceita dados do peca/Insumo
     */
    public function test_cadastro_dto_aceita_dados_peca_Insumo(): void
    {
        $dto = new CadastroDto(
            gtin: '7891234567890',
            descricao: 'Filtro de Óleo',
            valor_custo: 25.50,
            valor_venda: 45.90,
            qtd_atual: 100,
            qtd_segregada: 5,
            status: 'ativo'
        );

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEquals('7891234567890', $result['gtin']);
        $this->assertEquals('Filtro de Óleo', $result['descricao']);
        $this->assertEquals(25.50, $result['valor_custo']);
        $this->assertEquals(45.90, $result['valor_venda']);
    }

    // ==================== TESTES DO MODEL ====================

    /**
     * Teste se o model PecaInsumo pode ser instanciado
     */
    public function test_peca_Insumo_model_pode_ser_instanciado(): void
    {
        $peca_Insumo = new PecaInsumo();
        $this->assertInstanceOf(PecaInsumo::class, $peca_Insumo);
    }

    /**
     * Teste se o model tem a tabela correta configurada
     */
    public function test_peca_Insumo_model_tem_tabela_correta(): void
    {
        $peca_Insumo = new PecaInsumo();

        $this->assertEquals('peca_insumo', $peca_Insumo->getTable());
    }

    /**
     * Teste se o model tem os campos fillable corretos
     */
    public function test_peca_Insumo_model_tem_fillable_corretos(): void
    {
        $peca_Insumo = new PecaInsumo();

        $expectedFillable = [
            'gtin',
            'descricao',
            'valor_custo',
            'valor_venda',
            'qtd_atual',
            'qtd_segregada',
            'status',
            'excluido',
            'data_cadastro',
            'data_atualizacao',
            'data_exclusao'
        ];

        $this->assertEquals($expectedFillable, $peca_Insumo->getFillable());
    }

    /**
     * Teste se o model pode receber dados via construtor
     */
    public function test_peca_Insumo_model_pode_receber_dados_construtor(): void
    {
        $dados = [
            'gtin' => '7891234567890',
            'descricao' => 'Pastilha de Freio',
            'valor_custo' => 55.00,
            'valor_venda' => 95.00,
            'qtd_atual' => 50,
            'qtd_segregada' => 2,
            'status' => 'ativo',
            'excluido' => 0
        ];

        $peca_Insumo = new PecaInsumo($dados);

        $this->assertEquals('7891234567890', $peca_Insumo->gtin);
        $this->assertEquals('Pastilha de Freio', $peca_Insumo->descricao);
        $this->assertEquals(55.00, $peca_Insumo->valor_custo);
        $this->assertEquals(95.00, $peca_Insumo->valor_venda);
        $this->assertEquals(50, $peca_Insumo->qtd_atual);
    }

    /**
     * Teste se o model tem timestamps desabilitados
     */
    public function test_peca_Insumo_model_timestamps_desabilitados(): void
    {
        $peca_Insumo = new PecaInsumo();

        $this->assertFalse($peca_Insumo->timestamps);
    }

    // ==================== TESTES DO REPOSITORY ====================

    /**
     * Teste se o repository pode ser instanciado
     */
    public function test_peca_Insumo_repository_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\PecaInsumo\Repository\PecaInsumoRepository();

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class, $repository);
    }

    /**
     * Teste se o repository herda do AbstractRepository
     */
    public function test_peca_Insumo_repository_herda_abstract_repository(): void
    {
        $repository = new \App\Modules\PecaInsumo\Repository\PecaInsumoRepository();

        $this->assertInstanceOf(\App\AbstractRepository::class, $repository);
    }

    /**
     * Teste se o repository implementa RepositoryInterface
     */
    public function test_peca_Insumo_repository_implementa_interface(): void
    {
        $repository = new \App\Modules\PecaInsumo\Repository\PecaInsumoRepository();

        $this->assertInstanceOf(\App\Interfaces\RepositoryInterface::class, $repository);
    }

    /**
     * Teste se o repository tem o model correto configurado
     */
    public function test_peca_Insumo_repository_tem_model_correto(): void
    {
        $model = \App\Modules\PecaInsumo\Repository\PecaInsumoRepository::model();

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Model\PecaInsumo::class, $model);
    }

    /**
     * Teste se o repository tem métodos obrigatórios do AbstractRepository
     */
    public function test_peca_Insumo_repository_tem_metodos_obrigatorios(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class, 'model'));
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class, 'read'));
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class, 'findOne'));
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class, 'create'));
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class, 'update'));
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Repository\PecaInsumoRepository::class, 'delete'));
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_peca_Insumo_service_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\PecaInsumo\Repository\PecaInsumoRepository();
        $service = new \App\Modules\PecaInsumo\Service\Service($repository);

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Service\Service::class, $service);
    }

    public function test_atualizacao_retorna_array_vazio_quando_dados_vazios(): void
    {
        // Criar uma PecaInsumo usando factory e forçar refresh para garantir que o UUID existe
        $pecaInsumo = \App\Modules\PecaInsumo\Model\PecaInsumo::factory()->createOne()->fresh();

        // Verificar se o UUID foi criado corretamente
        $this->assertNotNull($pecaInsumo->uuid, 'O UUID da PecaInsumo não pode ser null');
        $this->assertIsString($pecaInsumo->uuid, 'O UUID deve ser uma string');

        // Mock do DTO que retorna apenas UUID
        $dto = $this->mock(\App\Modules\PecaInsumo\Dto\AtualizacaoDto::class, function ($mock) use ($pecaInsumo) {
            $mock->shouldReceive('asArray')
                ->once()
                ->andReturn(['uuid' => $pecaInsumo->uuid]); // Apenas UUID será removido, deixando array vazio
        });

        $service = app(\App\Modules\PecaInsumo\Service\Service::class);
        $resultado = $service->atualizacao($pecaInsumo->uuid, $dto);

        $this->assertEquals([], $resultado);
    }

    /**
     * Teste se o service tem método de listagem
     */
    public function test_peca_Insumo_service_tem_metodo_listagem(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Service\Service::class, 'listagem'));
    }

    /**
     * Teste se o service tem método de cadastro
     */
    public function test_peca_Insumo_service_tem_metodo_cadastro(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Service\Service::class, 'cadastro'));
    }

    /**
     * Teste se o service tem método obterUmPorUuid
     */
    public function test_peca_Insumo_service_tem_metodo_obter_um_por_uuid(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Service\Service::class, 'obterUmPorUuid'));
    }

    /**
     * Teste se o service tem método de remoção
     */
    public function test_peca_Insumo_service_tem_metodo_remocao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Service\Service::class, 'remocao'));
    }

    /**
     * Teste se o service tem método de atualização
     */
    public function test_peca_Insumo_service_tem_metodo_atualizacao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Service\Service::class, 'atualizacao'));
    }

    /**
     * Teste se o service recebe repository via construtor
     */
    public function test_peca_Insumo_service_recebe_repository_construtor(): void
    {
        $repository = new \App\Modules\PecaInsumo\Repository\PecaInsumoRepository();
        $service = new \App\Modules\PecaInsumo\Service\Service($repository);

        // Verifica se o service foi criado corretamente
        $this->assertInstanceOf(\App\Modules\PecaInsumo\Service\Service::class, $service);
    }

    // ==================== TESTES DO CONTROLLER ====================

    /**
     * Teste se o controller pode ser instanciado
     */
    public function test_peca_Insumo_controller_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\PecaInsumo\Repository\PecaInsumoRepository();
        $service = new \App\Modules\PecaInsumo\Service\Service($repository);
        $controller = new \App\Modules\PecaInsumo\Controller\PecaInsumoController($service);

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class, $controller);
    }

    /**
     * Teste se o controller herda do Controller base
     */
    public function test_peca_Insumo_controller_herda_controller_base(): void
    {
        $repository = new \App\Modules\PecaInsumo\Repository\PecaInsumoRepository();
        $service = new \App\Modules\PecaInsumo\Service\Service($repository);
        $controller = new \App\Modules\PecaInsumo\Controller\PecaInsumoController($service);

        $this->assertInstanceOf(\App\Http\Controllers\Controller::class, $controller);
    }

    /**
     * Teste se o controller tem método de listagem
     */
    public function test_peca_Insumo_controller_tem_metodo_listagem(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class, 'listagem'));
    }

    /**
     * Teste se o controller tem método de cadastro
     */
    public function test_peca_Insumo_controller_tem_metodo_cadastro(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class, 'cadastro'));
    }

    /**
     * Teste se o controller tem método obterUmPorUuid
     */
    public function test_peca_Insumo_controller_tem_metodo_obter_um_por_uuid(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class, 'obterUmPorUuid'));
    }

    /**
     * Teste se o controller tem método de remoção
     */
    public function test_peca_Insumo_controller_tem_metodo_remocao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class, 'remocao'));
    }

    /**
     * Teste se o controller tem método de atualização
     */
    public function test_peca_Insumo_controller_tem_metodo_atualizacao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class, 'atualizacao'));
    }

    /**
     * Teste se o controller recebe service via construtor
     */
    public function test_peca_Insumo_controller_recebe_service_construtor(): void
    {
        $repository = new \App\Modules\PecaInsumo\Repository\PecaInsumoRepository();
        $service = new \App\Modules\PecaInsumo\Service\Service($repository);
        $controller = new \App\Modules\PecaInsumo\Controller\PecaInsumoController($service);


        $this->assertInstanceOf(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class, $controller);
    }

    // ==================== TESTES DOS REQUESTS ====================

    /**
     * Teste se o CadastroRequest pode ser instanciado
     */
    public function test_peca_Insumo_cadastro_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\PecaInsumo\Requests\CadastroRequest();

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Requests\CadastroRequest::class, $request);
    }

    /**
     * Teste se o CadastroRequest herda do FormRequest
     */
    public function test_peca_Insumo_cadastro_request_herda_form_request(): void
    {
        $request = new \App\Modules\PecaInsumo\Requests\CadastroRequest();

        $this->assertInstanceOf(\Illuminate\Foundation\Http\FormRequest::class, $request);
    }

    /**
     * Teste se o CadastroRequest tem método rules
     */
    public function test_peca_Insumo_cadastro_request_tem_metodo_rules(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Requests\CadastroRequest::class, 'rules'));
    }

    /**
     * Teste se o CadastroRequest tem método toDto
     */
    public function test_peca_Insumo_cadastro_request_tem_metodo_to_dto(): void
    {
        $this->assertTrue(method_exists(\App\Modules\PecaInsumo\Requests\CadastroRequest::class, 'toDto'));
    }

    /**
     * Teste se o ListagemRequest pode ser instanciado
     */
    public function test_peca_Insumo_listagem_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\PecaInsumo\Requests\ListagemRequest();

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Requests\ListagemRequest::class, $request);
    }

    /**
     * Teste se o ObterUmPorIdRequest pode ser instanciado
     */
    public function test_peca_Insumo_obter_um_por_uuid_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\PecaInsumo\Requests\ObterUmPorUuidRequest();

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Requests\ObterUmPorUuidRequest::class, $request);
    }

    /**
     * Teste se o AtualizacaoRequest pode ser instanciado
     */
    public function test_peca_Insumo_atualizacao_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\PecaInsumo\Requests\AtualizacaoRequest();

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Requests\AtualizacaoRequest::class, $request);
    }

    // ==================== TESTES DAS ROUTES ====================

    /**
     * Teste se pode instanciar uma rota básica
     */
    public function test_peca_Insumo_pode_definir_rotas(): void
    {
        // Teste simples que verifica se as classes necessárias existem
        $this->assertTrue(class_exists(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class));
        $this->assertTrue(class_exists(\Illuminate\Support\Facades\Route::class));
    }

    /**
     * Teste se o namespace do controller está correto para as rotas
     */
    public function test_peca_Insumo_controller_namespace_correto(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class);
        $namespace = $reflection->getNamespaceName();

        $this->assertEquals('App\Modules\PecaInsumo\Controller', $namespace);
    }

    /**
     * Teste se o controller tem os métodos necessários para as rotas
     */
    public function test_peca_Insumo_controller_tem_metodos_para_rotas(): void
    {
        $controller = \App\Modules\PecaInsumo\Controller\PecaInsumoController::class;

        $this->assertTrue(method_exists($controller, 'listagem'));
        $this->assertTrue(method_exists($controller, 'cadastro'));
        $this->assertTrue(method_exists($controller, 'obterUmPorUuid'));
        $this->assertTrue(method_exists($controller, 'atualizacao'));
        $this->assertTrue(method_exists($controller, 'remocao'));
    }

    /**
     * Teste se as constantes HTTP estão disponíveis para as rotas
     */
    public function test_peca_Insumo_constantes_http_disponiveis(): void
    {
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_OK'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_CREATED'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND'));
    }

    // ==================== TESTES DO SWAGGER ====================

    /**
     * Teste se o controller tem anotações OpenApi
     */
    public function test_peca_Insumo_controller_tem_anotacoes_openapi(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class);
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
    public function test_peca_Insumo_controller_listagem_tem_swagger(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\PecaInsumo\Controller\PecaInsumoController::class);
        $method = $reflection->getMethod('listagem');
        $docComment = $method->getDocComment();

        $this->assertStringContainsString('@OA\\Get', $docComment);
        $this->assertStringContainsString('/api/peca_insumo', $docComment);
        $this->assertStringContainsString('PecaInsumo', $docComment);
    }

}
