<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Modules\Servico\Dto\ListagemDto;
use App\Modules\Servico\Dto\CadastroDto;
use App\Modules\Servico\Model\Servico;

class ServicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_servico_dto_retorna_array_vazio_inicialmente(): void
    {
        $dto = new ListagemDto();

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_cadastro_dto_aceita_dados_servico(): void
    {
        $dto = new CadastroDto(
            descricao: 'Troca de Óleo',
            valor: 25.50,
            status: 'ATIVO'
        );

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEquals('Troca de Óleo', $result['descricao']);
        $this->assertEquals(25.50, $result['valor']);
        $this->assertEquals('ATIVO', $result['status']);
    }

    // // ==================== TESTES DO MODEL ====================

    public function test_servico_model_pode_ser_instanciado(): void
    {
        $servico = new Servico();
        $this->assertInstanceOf(Servico::class, $servico);
    }

    public function test_servico_model_tem_tabela_correta(): void
    {
        $servico = new Servico();

        $this->assertEquals('servicos', $servico->getTable());
    }

    public function test_servico_model_tem_fillable_corretos(): void
    {
        $servico = new Servico();

        $expectedFillable = [
            'descricao',
            'valor',
            'status',
            'excluido',
            'data_cadastro',
            'data_exclusao',
            'data_atualizacao',
        ];

        $this->assertEquals($expectedFillable, $servico->getFillable());
    }

    public function test_servico_model_pode_receber_dados_construtor(): void
    {
        $dados = [
            'descricao' => 'Troca de Óleo',
            'valor' => 25.50,
            'status' => 'ATIVO'
        ];

        $servico = new Servico($dados);

        $this->assertEquals('Troca de Óleo', $servico->descricao);
        $this->assertEquals(25.50, $servico->valor);
        $this->assertEquals('ATIVO', $servico->status);
    }

    public function test_servico_model_timestamps_desabilitados(): void
    {
        $servico = new Servico();

        $this->assertFalse($servico->timestamps);
    }

    // // ==================== TESTES DO REPOSITORY ====================

    public function test_servico_repository_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\Servico\Repository\ServicoRepository();

        $this->assertInstanceOf(\App\Modules\Servico\Repository\ServicoRepository::class, $repository);
    }

    public function test_servico_repository_herda_abstract_repository(): void
    {
        $repository = new \App\Modules\Servico\Repository\ServicoRepository();

        $this->assertInstanceOf(\App\AbstractRepository::class, $repository);
    }

    public function test_servico_repository_implementa_interface(): void
    {
        $repository = new \App\Modules\Servico\Repository\ServicoRepository();

        $this->assertInstanceOf(\App\Interfaces\RepositoryInterface::class, $repository);
    }

    public function test_servico_repository_tem_model_correto(): void
    {
        $model = \App\Modules\PecaInsumo\Repository\PecaInsumoRepository::model();

        $this->assertInstanceOf(\App\Modules\PecaInsumo\Model\PecaInsumo::class, $model);
    }

    public function test_servico_repository_tem_metodos_obrigatorios(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Repository\ServicoRepository::class, 'model'));
        $this->assertTrue(method_exists(\App\Modules\Servico\Repository\ServicoRepository::class, 'read'));
        $this->assertTrue(method_exists(\App\Modules\Servico\Repository\ServicoRepository::class, 'findOne'));
        $this->assertTrue(method_exists(\App\Modules\Servico\Repository\ServicoRepository::class, 'create'));
        $this->assertTrue(method_exists(\App\Modules\Servico\Repository\ServicoRepository::class, 'update'));
        $this->assertTrue(method_exists(\App\Modules\Servico\Repository\ServicoRepository::class, 'delete'));
    }

    // // ==================== TESTES DO SERVICE ====================

    public function test_servico_service_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\Servico\Repository\ServicoRepository();
        $service = new \App\Modules\Servico\Service\Service($repository);

        $this->assertInstanceOf(\App\Modules\Servico\Service\Service::class, $service);
    }

    public function test_atualizacao_retorna_array_vazio_quando_dados_vazios(): void
    {
        // Criar uma Servico usando factory e forçar refresh para garantir que o UUID existe
        $servico = \App\Modules\Servico\Model\Servico::factory()->createOne()->fresh();

        // Verificar se o UUID foi criado corretamente
        $this->assertNotNull($servico->uuid, 'O UUID da Servico não pode ser null');
        $this->assertIsString($servico->uuid, 'O UUID deve ser uma string');

        // Mock do DTO que retorna apenas UUID
        $dto = $this->mock(\App\Modules\Servico\Dto\AtualizacaoDto::class, function ($mock) use ($servico) {
            $mock->shouldReceive('asArray')
                ->once()
                ->andReturn(['uuid' => $servico->uuid]); // Apenas UUID será removido, deixando array vazio
        });

        $service = app(\App\Modules\Servico\Service\Service::class);
        $resultado = $service->atualizacao($servico->uuid, $dto);

        $this->assertEquals([], $resultado);
    }

    public function test_servico_service_tem_metodo_listagem(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Service\Service::class, 'listagem'));
    }

    public function test_servico_service_tem_metodo_cadastro(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Service\Service::class, 'cadastro'));
    }

    public function test_servico_service_tem_metodo_obter_um_por_uuid(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Service\Service::class, 'obterUmPorUuid'));
    }

    public function test_servico_service_tem_metodo_remocao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Service\Service::class, 'remocao'));
    }

    public function test_servico_service_tem_metodo_atualizacao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Service\Service::class, 'atualizacao'));
    }

    public function test_servico_service_recebe_repository_construtor(): void
    {
        $repository = new \App\Modules\Servico\Repository\ServicoRepository();
        $service = new \App\Modules\Servico\Service\Service($repository);

        // Verifica se o service foi criado corretamente
        $this->assertInstanceOf(\App\Modules\Servico\Service\Service::class, $service);
    }

    // // ==================== TESTES DO CONTROLLER ====================

    public function test_servico_controller_pode_ser_instanciado(): void
    {
        $repository = new \App\Modules\Servico\Repository\ServicoRepository();
        $service = new \App\Modules\Servico\Service\Service($repository);
        $controller = new \App\Modules\Servico\Controller\ServicoController($service);

        $this->assertInstanceOf(\App\Modules\Servico\Controller\ServicoController::class, $controller);
    }

    public function test_servico_controller_herda_controller_base(): void
    {
        $repository = new \App\Modules\Servico\Repository\ServicoRepository();
        $service = new \App\Modules\Servico\Service\Service($repository);
        $controller = new \App\Modules\Servico\Controller\ServicoController($service);

        $this->assertInstanceOf(\App\Http\Controllers\Controller::class, $controller);
    }

    public function test_servico_controller_tem_metodo_listagem(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Controller\ServicoController::class, 'listagem'));
    }

    public function test_servico_controller_tem_metodo_cadastro(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Controller\ServicoController::class, 'cadastro'));
    }

    public function test_servico_controller_tem_metodo_obter_um_por_uuid(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Controller\ServicoController::class, 'obterUmPorUuid'));
    }

    public function test_servico_controller_tem_metodo_remocao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Controller\ServicoController::class, 'remocao'));
    }

    public function test_servico_controller_tem_metodo_atualizacao(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Controller\ServicoController::class, 'atualizacao'));
    }

    public function test_servico_controller_recebe_service_construtor(): void
    {
        $repository = new \App\Modules\Servico\Repository\ServicoRepository();
        $service = new \App\Modules\Servico\Service\Service($repository);
        $controller = new \App\Modules\Servico\Controller\ServicoController($service);

        $this->assertInstanceOf(\App\Modules\Servico\Controller\ServicoController::class, $controller);
    }

    // // ==================== TESTES DOS REQUESTS ====================

    public function test_servico_cadastro_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\Servico\Requests\CadastroRequest();

        $this->assertInstanceOf(\App\Modules\Servico\Requests\CadastroRequest::class, $request);
    }

    public function test_servico_cadastro_request_herda_form_request(): void
    {
        $request = new \App\Modules\Servico\Requests\CadastroRequest();

        $this->assertInstanceOf(\Illuminate\Foundation\Http\FormRequest::class, $request);
    }

    public function test_servico_cadastro_request_tem_metodo_rules(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Requests\CadastroRequest::class, 'rules'));
    }

    public function test_servico_cadastro_request_tem_metodo_to_dto(): void
    {
        $this->assertTrue(method_exists(\App\Modules\Servico\Requests\CadastroRequest::class, 'toDto'));
    }

    public function test_servico_listagem_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\Servico\Requests\ListagemRequest();

        $this->assertInstanceOf(\App\Modules\Servico\Requests\ListagemRequest::class, $request);
    }

    public function test_servico_obter_um_por_uuid_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\Servico\Requests\ObterUmPorUuidRequest();

        $this->assertInstanceOf(\App\Modules\Servico\Requests\ObterUmPorUuidRequest::class, $request);
    }

    public function test_servico_atualizacao_request_pode_ser_instanciado(): void
    {
        $request = new \App\Modules\Servico\Requests\AtualizacaoRequest();

        $this->assertInstanceOf(\App\Modules\Servico\Requests\AtualizacaoRequest::class, $request);
    }

    // // ==================== TESTES DAS ROUTES ====================

    public function test_servico_pode_definir_rotas(): void
    {
        // Teste simples que verifica se as classes necessárias existem
        $this->assertTrue(class_exists(\App\Modules\Servico\Controller\ServicoController::class));
        $this->assertTrue(class_exists(\Illuminate\Support\Facades\Route::class));
    }

    public function test_servico_controller_namespace_correto(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\Servico\Controller\ServicoController::class);
        $namespace = $reflection->getNamespaceName();

        $this->assertEquals('App\Modules\Servico\Controller', $namespace);
    }

    public function test_servico_controller_tem_metodos_para_rotas(): void
    {
        $controller = \App\Modules\Servico\Controller\ServicoController::class;

        $this->assertTrue(method_exists($controller, 'listagem'));
        $this->assertTrue(method_exists($controller, 'cadastro'));
        $this->assertTrue(method_exists($controller, 'obterUmPorUuid'));
        $this->assertTrue(method_exists($controller, 'atualizacao'));
        $this->assertTrue(method_exists($controller, 'remocao'));
    }

    public function test_servico_constantes_http_disponiveis(): void
    {
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_OK'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_CREATED'));
        $this->assertTrue(defined('Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND'));
    }

    // // ==================== TESTES DO SWAGGER ====================

    public function test_servico_controller_tem_anotacoes_openapi(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\Servico\Controller\ServicoController::class);
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

    public function test_servico_controller_listagem_tem_swagger(): void
    {
        $reflection = new \ReflectionClass(\App\Modules\Servico\Controller\ServicoController::class);
        $method = $reflection->getMethod('listagem');
        $docComment = $method->getDocComment();

        $this->assertStringContainsString('@OA\\Get', $docComment);
        $this->assertStringContainsString('/api/servico', $docComment);
        $this->assertStringContainsString('servico', $docComment);
    }

}
