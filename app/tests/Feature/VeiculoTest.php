<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Modules\Veiculo\Dto\ListagemDto;
use App\Modules\Veiculo\Dto\CadastroDto;
use App\Modules\Veiculo\Model\Veiculo;

class VeiculoTest extends TestCase
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
     * Teste se o DTO de cadastro aceita dados do veículo
     */
    public function test_cadastro_dto_aceita_dados_veiculo(): void
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
     * Teste se o model Veiculo pode ser instanciado
     */
    public function test_veiculo_model_pode_ser_instanciado(): void
    {
        $veiculo = new Veiculo();

        $this->assertInstanceOf(Veiculo::class, $veiculo);
    }

    /**
     * Teste se o model tem a tabela correta configurada
     */
    public function test_veiculo_model_tem_tabela_correta(): void
    {
        $veiculo = new Veiculo();

        $this->assertEquals('veiculo', $veiculo->getTable());
    }

    /**
     * Teste se o model tem os campos fillable corretos
     */
    public function test_veiculo_model_tem_fillable_corretos(): void
    {
        $veiculo = new Veiculo();

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

        $this->assertEquals($expectedFillable, $veiculo->getFillable());
    }

    /**
     * Teste se o model pode receber dados via construtor
     */
    public function test_veiculo_model_pode_receber_dados_construtor(): void
    {
        $dados = [
            'uuid' => 'test-uuid-123',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'placa' => 'ABC-1234',
            'ano_fabricacao' => 2020,
            'excluido' => 0
        ];

        $veiculo = new Veiculo($dados);

        $this->assertEquals('Toyota', $veiculo->marca);
        $this->assertEquals('Corolla', $veiculo->modelo);
        $this->assertEquals('ABC-1234', $veiculo->placa);
        $this->assertEquals(2020, $veiculo->ano_fabricacao);
    }

    /**
     * Teste se o model tem timestamps desabilitados
     */
    public function test_veiculo_model_timestamps_desabilitados(): void
    {
        $veiculo = new Veiculo();

        $this->assertFalse($veiculo->timestamps);
    }

    // ==================== TESTES DO REPOSITORY ====================

    /**
     * Teste se o repository pode ser instanciado
     */
    public function test_veiculo_repository_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\Veiculo\Repository\VeiculoRepository();

        $this->assertInstanceOf(\App\Modules\Veiculo\Repository\VeiculoRepository::class, $repository);
    }

    /**
     * Teste se o repository herda do AbstractRepository
     */
    public function test_veiculo_repository_herda_abstract_repository(): void
    {
        $repository = new \App\Modules\Veiculo\Repository\VeiculoRepository();

        $this->assertInstanceOf(\App\AbstractRepository::class, $repository);
    }

    /**
     * Teste se o repository implementa RepositoryInterface
     */
    public function test_veiculo_repository_implementa_interface(): void
    {
        $repository = new \App\Modules\Veiculo\Repository\VeiculoRepository();

        $this->assertInstanceOf(\App\Interfaces\RepositoryInterface::class, $repository);
    }

    /**
     * Teste se o repository tem o model correto configurado
     */
    public function test_veiculo_repository_tem_model_correto(): void
    {
        $model = \App\Modules\Veiculo\Repository\VeiculoRepository::model();

        $this->assertInstanceOf(\App\Modules\Veiculo\Model\Veiculo::class, $model);
    }

    /**
     * Teste se o repository tem métodos obrigatórios do AbstractRepository
     */
    public function test_veiculo_repository_tem_metodos_obrigatorios(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Repository\VeiculoRepository::class, 'model'));
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Repository\VeiculoRepository::class, 'read'));
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Repository\VeiculoRepository::class, 'findOne'));
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Repository\VeiculoRepository::class, 'create'));
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Repository\VeiculoRepository::class, 'update'));
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Repository\VeiculoRepository::class, 'delete'));
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_veiculo_service_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\Veiculo\Repository\VeiculoRepository();
        $service = new \App\Modules\Veiculo\Service\Service($repository);

        $this->assertInstanceOf(\App\Modules\Veiculo\Service\Service::class, $service);
    }

    /**
     * Teste se o service tem método de listagem
     */
    public function test_veiculo_service_tem_metodo_listagem(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Service\Service::class, 'listagem'));
    }

    /**
     * Teste se o service tem método de cadastro
     */
    public function test_veiculo_service_tem_metodo_cadastro(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Service\Service::class, 'cadastro'));
    }

    /**
     * Teste se o service tem método obterUmPorUuid
     */
    public function test_veiculo_service_tem_metodo_obter_um_por_uuid(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Service\Service::class, 'obterUmPorUuid'));
    }

    /**
     * Teste se o service tem método de remoção
     */
    public function test_veiculo_service_tem_metodo_remocao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Service\Service::class, 'remocao'));
    }

    /**
     * Teste se o service tem método de atualização
     */
    public function test_veiculo_service_tem_metodo_atualizacao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Service\Service::class, 'atualizacao'));
    }

    /**
     * Teste se o service recebe repository via construtor
     */
    public function test_veiculo_service_recebe_repository_construtor(): void
    {
        $repository = new \App\Modules\Veiculo\Repository\VeiculoRepository();
        $service = new \App\Modules\Veiculo\Service\Service($repository);

        // Verifica se o service foi criado corretamente
        $this->assertInstanceOf(\App\Modules\Veiculo\Service\Service::class, $service);
    }

    // ==================== TESTES DO CONTROLLER ====================

    /**
     * Teste se o controller pode ser instanciado
     */
    public function test_veiculo_controller_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\Veiculo\Repository\VeiculoRepository();
        $service = new \App\Modules\Veiculo\Service\Service($repository);
        $controller = new \App\Modules\Veiculo\Controller\VeiculoController($service);

        $this->assertInstanceOf(\App\Modules\Veiculo\Controller\VeiculoController::class, $controller);
    }

    /**
     * Teste se o controller herda do Controller base
     */
    public function test_veiculo_controller_herda_controller_base(): void
    {
        $repository = new \App\Modules\Veiculo\Repository\VeiculoRepository();
        $service = new \App\Modules\Veiculo\Service\Service($repository);
        $controller = new \App\Modules\Veiculo\Controller\VeiculoController($service);

        $this->assertInstanceOf(\App\Http\Controllers\Controller::class, $controller);
    }

    /**
     * Teste se o controller tem método de listagem
     */
    public function test_veiculo_controller_tem_metodo_listagem(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Controller\VeiculoController::class, 'listagem'));
    }

    /**
     * Teste se o controller tem método de cadastro
     */
    public function test_veiculo_controller_tem_metodo_cadastro(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Controller\VeiculoController::class, 'cadastro'));
    }

    /**
     * Teste se o controller tem método obterUmPorUuid
     */
    public function test_veiculo_controller_tem_metodo_obter_um_por_uuid(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Controller\VeiculoController::class, 'obterUmPorUuid'));
    }

    /**
     * Teste se o controller tem método de remoção
     */
    public function test_veiculo_controller_tem_metodo_remocao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Controller\VeiculoController::class, 'remocao'));
    }

    /**
     * Teste se o controller tem método de atualização
     */
    public function test_veiculo_controller_tem_metodo_atualizacao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Controller\VeiculoController::class, 'atualizacao'));
    }

    /**
     * Teste se o controller recebe service via construtor
     */
    public function test_veiculo_controller_recebe_service_construtor(): void
    {
        $repository = new \App\Modules\Veiculo\Repository\VeiculoRepository();
        $service = new \App\Modules\Veiculo\Service\Service($repository);
        $controller = new \App\Modules\Veiculo\Controller\VeiculoController($service);


        $this->assertInstanceOf(\App\Modules\Veiculo\Controller\VeiculoController::class, $controller);
    }

    // ==================== TESTES DOS REQUESTS ====================

    /**
     * Teste se o CadastroRequest pode ser instanciado
     */
    public function test_veiculo_cadastro_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\Veiculo\Requests\CadastroRequest();

        $this->assertInstanceOf(\App\Modules\Veiculo\Requests\CadastroRequest::class, $request);
    }

    /**
     * Teste se o CadastroRequest herda do FormRequest
     */
    public function test_veiculo_cadastro_request_herda_form_request(): void
    {
        $request = new \App\Modules\Veiculo\Requests\CadastroRequest();

        $this->assertInstanceOf(\Illuminate\Foundation\Http\FormRequest::class, $request);
    }

    /**
     * Teste se o CadastroRequest tem método rules
     */
    public function test_veiculo_cadastro_request_tem_metodo_rules(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Requests\CadastroRequest::class, 'rules'));
    }

    /**
     * Teste se o CadastroRequest tem método toDto
     */
    public function test_veiculo_cadastro_request_tem_metodo_to_dto(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Veiculo\Requests\CadastroRequest::class, 'toDto'));
    }

    /**
     * Teste se o ListagemRequest pode ser instanciado
     */
    public function test_veiculo_listagem_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\Veiculo\Requests\ListagemRequest();

        $this->assertInstanceOf(\App\Modules\Veiculo\Requests\ListagemRequest::class, $request);
    }

    /**
     * Teste se o ObterUmPorUuidRequest pode ser instanciado
     */
    public function test_veiculo_obter_um_por_uuid_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\Veiculo\Requests\ObterUmPorUuidRequest();

        $this->assertInstanceOf(\App\Modules\Veiculo\Requests\ObterUmPorUuidRequest::class, $request);
    }

    /**
     * Teste se o AtualizacaoRequest pode ser instanciado
     */
    public function test_veiculo_atualizacao_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\Veiculo\Requests\AtualizacaoRequest();

        $this->assertInstanceOf(\App\Modules\Veiculo\Requests\AtualizacaoRequest::class, $request);
    }

    // ==================== TESTES DAS ROUTES ====================

    /**
     * Teste se pode instanciar uma rota básica
     */
    public function test_veiculo_pode_definir_rotas(): void
    {
        // Teste simples que verifica se as classes necessárias existem
        $this->assertTrue(class_exists(\App\Modules\Veiculo\Controller\VeiculoController::class));
        $this->assertTrue(class_exists(\Illuminate\Support\Facades\Route::class));
    }

    /**
     * Teste se o namespace do controller está correto para as rotas
     */
    public function test_veiculo_controller_namespace_correto(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\Veiculo\Controller\VeiculoController::class);
        $namespace = $reflection->getNamespaceName();

        $this->assertEquals('App\Modules\Veiculo\Controller', $namespace);
    }

    /**
     * Teste se o controller tem os métodos necessários para as rotas
     */
    public function test_veiculo_controller_tem_metodos_para_rotas(): void
    {
        $controller = \App\Modules\Veiculo\Controller\VeiculoController::class;

        $this->assertTrue(method_exists($controller, 'listagem'));
        $this->assertTrue(method_exists($controller, 'cadastro'));
        $this->assertTrue(method_exists($controller, 'obterUmPorUuid'));
        $this->assertTrue(method_exists($controller, 'atualizacao'));
        $this->assertTrue(method_exists($controller, 'remocao'));
    }

    /**
     * Teste se as constantes HTTP estão disponíveis para as rotas
     */
    public function test_veiculo_constantes_http_disponiveis(): void
    {
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_OK'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_CREATED'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND'));
    }

    // ==================== TESTES DO SWAGGER ====================

    /**
     * Teste se o controller tem anotações OpenApi
     */
    public function test_veiculo_controller_tem_anotacoes_openapi(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\Veiculo\Controller\VeiculoController::class);
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
    public function test_veiculo_controller_listagem_tem_swagger(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\Veiculo\Controller\VeiculoController::class);
        $method = $reflection->getMethod('listagem');
        $docComment = $method->getDocComment();

        $this->assertStringContainsString('@OA\\Get', $docComment);
        $this->assertStringContainsString('/api/veiculo', $docComment);
        $this->assertStringContainsString('Veiculo', $docComment);
    }
}
