<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Modules\PecaInsumo\Dto\ListagemDto;
use App\Modules\PecaInsumo\Dto\CadastroDto;
use App\Modules\PecaInsumo\Model\PecaInsumo;

class PecaInsumoTest extends TestCase
{
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
            marca: 'Toyota',
            modelo: 'Corolla',
            ano: 2020,
            placa: 'ABC-1234',
            cor: 'Prata',
            chassi: '1234567890ABCDEFG'
        );

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEquals('Toyota', $result['marca']);
        $this->assertEquals('Corolla', $result['modelo']);
        $this->assertEquals(2020, $result['ano_fabricacao']);
        $this->assertEquals('ABC-1234', $result['placa']);
    }

    /**
     * Teste se o método filled funciona corretamente
     */
    public function test_filled_retorna_apenas_valores_preenchidos(): void
    {
        $dto = new CadastroDto(
            marca: 'Honda',
            modelo: 'Civic',
            ano: 2019,
            placa: 'XYZ-9876',
            cor: null, // valor nulo
            chassi: '0987654321ZYXWVUT'
        );

        $filled = $dto->filled();

        $this->assertArrayHasKey('marca', $filled);
        $this->assertArrayHasKey('modelo', $filled);
        $this->assertArrayHasKey('ano_fabricacao', $filled);
        $this->assertArrayHasKey('placa', $filled);
        $this->assertArrayHasKey('chassi', $filled);
        $this->assertArrayNotHasKey('cor', $filled); // não deve ter cor pois é null
    }

    /**
     * Teste se o DTO de listagem funciona corretamente
     */
    public function test_filled_retorna_apenas_valores_preenchidos_listagem(): void
    {
        $dto = new ListagemDto();

        $filled = $dto->filled();

        $this->assertIsArray($filled);
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

        $this->assertEquals('peca_Insumo', $peca_Insumo->getTable());
    }

    /**
     * Teste se o model tem os campos fillable corretos
     */
    public function test_peca_Insumo_model_tem_fillable_corretos(): void
    {
        $peca_Insumo = new PecaInsumo();

        $expectedFillable = [
            'uuid',
            'marca',
            'modelo',
            'placa',
            'ano_fabricacao',
            'cor',
            'chassi',
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
            'uuid' => 'test-uuid-123',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'placa' => 'ABC-1234',
            'ano_fabricacao' => 2020,
            'excluido' => 0
        ];

        $peca_Insumo = new PecaInsumo($dados);

        $this->assertEquals('Toyota', $peca_Insumo->marca);
        $this->assertEquals('Corolla', $peca_Insumo->modelo);
        $this->assertEquals('ABC-1234', $peca_Insumo->placa);
        $this->assertEquals(2020, $peca_Insumo->ano_fabricacao);
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
     * Teste se o ObterUmPorUuidRequest pode ser instanciado
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
        $this->assertStringContainsString('/api/peca_Insumo', $docComment);
        $this->assertStringContainsString('PecaInsumo', $docComment);
    }
}
