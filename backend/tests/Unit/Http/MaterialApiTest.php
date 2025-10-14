<?php

namespace Tests\Unit\Http;

use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;
use App\Infrastructure\Controller\Material as MaterialController;
use App\Http\MaterialApi;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;
use Exception;

class MaterialApiTest extends TestCase
{
    public function testCreateComNomeVazio()
    {
        // Arrange
        $request = Request::create('/material', 'POST', [
            'nome' => '',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.00,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum'
        ]);

        $controller = $this->createMock(MaterialController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('obrigatório', $data['msg']);
    }

    public function testCreateComGtinVazio()
    {
        // Arrange
        $request = Request::create('/material', 'POST', [
            'nome' => 'Parafuso',
            'gtin' => '',
            'preco_custo' => 10.50,
            'preco_venda' => 15.00,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum'
        ]);

        $controller = $this->createMock(MaterialController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('obrigatório', $data['msg']);
    }

    public function testCreateComPrecoVendaInvalido()
    {
        // Arrange
        $request = Request::create('/material', 'POST', [
            'nome' => 'Parafuso',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 'invalid',
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum'
        ]);

        $controller = $this->createMock(MaterialController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('casas decimais', $data['msg']);
    }

    public function testCreateComEstoqueInvalido()
    {
        // Arrange
        $request = Request::create('/material', 'POST', [
            'nome' => 'Parafuso',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.00,
            'preco_uso_interno' => 12.00,
            'estoque' => 'invalid',
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum'
        ]);

        $controller = $this->createMock(MaterialController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('número inteiro', $data['msg']);
    }

    public function testCastsCreateComPrecos()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'nome' => 'Parafuso',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.75,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum'
        ];

        // Act
        $resultado = $api->castsCreate($dados);

        // Assert
        $this->assertEquals('Parafuso', $resultado['nome']);
        $this->assertEquals('1234567890123', $resultado['gtin']);
        $this->assertEquals(1050, $resultado['preco_custo']); // 10.50 * 100
        $this->assertEquals(1575, $resultado['preco_venda']); // 15.75 * 100
        $this->assertEquals(1200, $resultado['preco_uso_interno']); // 12.00 * 100
        $this->assertEquals(100, $resultado['estoque']);
        $this->assertEquals('PAR001', $resultado['sku']);
        $this->assertEquals('Parafuso comum', $resultado['descricao']);
    }

    public function testCastsCreateComSkuNulo()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'nome' => 'Parafuso',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.75,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => null,
            'descricao' => 'Parafuso comum'
        ];

        // Act
        $resultado = $api->castsCreate($dados);

        // Assert
        $this->assertNull($resultado['sku']);
        $this->assertEquals('Parafuso', $resultado['nome']);
    }

    public function testCastsCreateComSkuVazio()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'nome' => 'Parafuso',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.75,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => '',
            'descricao' => 'Parafuso comum'
        ];

        // Act
        $resultado = $api->castsCreate($dados);

        // Assert
        $this->assertNull($resultado['sku']);
        $this->assertEquals('Parafuso', $resultado['nome']);
    }

    public function testCastsCreateComDescricaoNula()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'nome' => 'Parafuso',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.75,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => null
        ];

        // Act
        $resultado = $api->castsCreate($dados);

        // Assert
        $this->assertNull($resultado['descricao']);
        $this->assertEquals('Parafuso', $resultado['nome']);
    }

    public function testCastsCreateComEstoqueString()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'nome' => 'Parafuso',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.75,
            'preco_uso_interno' => 12.00,
            'estoque' => '100',
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum'
        ];

        // Act
        $resultado = $api->castsCreate($dados);

        // Assert
        $this->assertEquals(100, $resultado['estoque']);
        $this->assertIsInt($resultado['estoque']);
    }

    public function testCastsCreateComPrecosString()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'nome' => 'Parafuso',
            'gtin' => '1234567890123',
            'preco_custo' => '10.50',
            'preco_venda' => '15.75',
            'preco_uso_interno' => '12.00',
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum'
        ];

        // Act
        $resultado = $api->castsCreate($dados);

        // Assert
        $this->assertEquals(1000, $resultado['preco_custo']); // intval("10.50") = 10, 10 * 100 = 1000
        $this->assertEquals(1500, $resultado['preco_venda']); // intval("15.75") = 15, 15 * 100 = 1500
        $this->assertEquals(1200, $resultado['preco_uso_interno']); // intval("12.00") = 12, 12 * 100 = 1200
    }

    public function testReadComThrowableGenerico()
    {
        // Arrange
        $controller = $this->createMock(MaterialController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);
        $request = Request::create('/material', 'GET');

        // Mock controller throwing generic exception
        $controller->method('useRepositorio')
            ->with($repositorio)
            ->willReturnSelf();

        $controller->expects($this->once())
            ->method('listar')
            ->willThrowException(new Exception('Erro interno'));

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->read($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno', $data['msg']);
    }

    public function testReadOneComUuidInvalido()
    {
        // Arrange
        $controller = $this->createMock(MaterialController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);
        $request = Request::create('/material/invalid-uuid', 'GET');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->readOne($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('uuid', $data['msg']);
    }



    public function testDeleteComUuidInvalido()
    {
        // Arrange
        $controller = $this->createMock(MaterialController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);
        $request = Request::create('/material/invalid-uuid', 'DELETE');
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->delete($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('uuid', $data['msg']);
    }

    public function testCastsUpdateComPrecos()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'nome' => 'Parafuso',
            'preco_custo' => 10.50,
            'preco_venda' => 15.75,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum'
        ];

        // Act
        $resultado = $api->castsUpdate($dados);

        // Assert
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $resultado['uuid']);
        $this->assertEquals('Parafuso', $resultado['nome']);
        $this->assertEquals(1050, $resultado['preco_custo']); // 10.50 * 100
        $this->assertEquals(1575, $resultado['preco_venda']); // 15.75 * 100
        $this->assertEquals(1200, $resultado['preco_uso_interno']); // 12.00 * 100
        $this->assertEquals(100, $resultado['estoque']);
        $this->assertEquals('PAR001', $resultado['sku']);
        $this->assertEquals('Parafuso comum', $resultado['descricao']);
    }

    public function testCastsUpdateComSkuVazio()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'nome' => 'Parafuso',
            'sku' => '',
            'descricao' => 'Parafuso comum'
        ];

        // Act
        $resultado = $api->castsUpdate($dados);

        // Assert
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $resultado['uuid']);
        $this->assertEquals('Parafuso', $resultado['nome']);
        $this->assertEquals('Parafuso comum', $resultado['descricao']);
        $this->assertArrayNotHasKey('sku', $resultado); // SKU vazio deve ser removido
    }

    public function testCastsUpdateComCamposNulos()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'nome' => 'Parafuso',
            'preco_custo' => null,
            'preco_venda' => null,
            'preco_uso_interno' => null,
            'estoque' => null,
            'sku' => null,
            'descricao' => null
        ];

        // Act
        $resultado = $api->castsUpdate($dados);

        // Assert
        $this->assertEquals([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'nome' => 'Parafuso'
        ], $resultado);
    }

    public function testCastsUpdateComEstoqueString()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'nome' => 'Parafuso',
            'estoque' => '150'
        ];

        // Act
        $resultado = $api->castsUpdate($dados);

        // Assert
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $resultado['uuid']);
        $this->assertEquals('Parafuso', $resultado['nome']);
        $this->assertEquals(150, $resultado['estoque']);
        $this->assertIsInt($resultado['estoque']);
    }

    public function testCastsUpdateComPrecosString()
    {
        // Arrange
        $api = new MaterialApi(
            $this->createMock(MaterialController::class),
            new HttpJsonPresenter(),
            $this->createMock(MaterialRepositorio::class)
        );

        $dados = [
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'nome' => 'Parafuso',
            'preco_custo' => '10.50',
            'preco_venda' => '15.75',
            'preco_uso_interno' => '12.00'
        ];

        // Act
        $resultado = $api->castsUpdate($dados);

        // Assert
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $resultado['uuid']);
        $this->assertEquals('Parafuso', $resultado['nome']);
        $this->assertEquals(1000, $resultado['preco_custo']); // intval("10.50") = 10, 10 * 100 = 1000
        $this->assertEquals(1500, $resultado['preco_venda']); // intval("15.75") = 15, 15 * 100 = 1500
        $this->assertEquals(1200, $resultado['preco_uso_interno']); // intval("12.00") = 12, 12 * 100 = 1200
    }

    // ==================== TESTES DE SUCESSO ====================

    public function testCreateComSucesso()
    {
        // Arrange
        $request = Request::create('/material', 'POST', [
            'nome' => 'Parafuso M8',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.00,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum M8'
        ]);

        $expectedResult = ['uuid' => '123e4567-e89b-12d3-a456-426614174000'];

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('criar')
            ->with(
                'Parafuso M8',
                '1234567890123',
                100,
                1050,
                1500,
                1200,
                'PAR001',
                'Parafuso comum M8'
            )
            ->willReturn($expectedResult);

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_CREATED, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertEquals($expectedResult, $data);
    }

    public function testCreateComException()
    {
        // Arrange
        $request = Request::create('/material', 'POST', [
            'nome' => 'Parafuso M8',
            'gtin' => '1234567890123',
            'preco_custo' => 10.50,
            'preco_venda' => 15.00,
            'preco_uso_interno' => 12.00,
            'estoque' => 100,
            'sku' => 'PAR001',
            'descricao' => 'Parafuso comum M8'
        ]);

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('criar')
            ->willThrowException(new Exception('Erro interno do servidor'));

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->create($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno do servidor', $data['msg']);
    }

    public function testReadComSucesso()
    {
        // Arrange
        $request = Request::create('/material', 'GET');

        $expectedResult = [
            ['uuid' => '123e4567-e89b-12d3-a456-426614174000', 'nome' => 'Material 1'],
            ['uuid' => '123e4567-e89b-12d3-a456-426614174001', 'nome' => 'Material 2']
        ];

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('listar')
            ->willReturn($expectedResult);

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->read($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertEquals($expectedResult, $data);
    }

    public function testReadComDomainException()
    {
        // Arrange
        $request = Request::create('/material', 'GET');

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('listar')
            ->willThrowException(new \App\Exception\DomainHttpException('Erro ao listar materiais', Response::HTTP_BAD_REQUEST));

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->read($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro ao listar materiais', $data['msg']);
    }

    public function testReadOneComSucesso()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'GET');
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $expectedResult = [
            'uuid' => $uuid,
            'nome' => 'Parafuso M8',
            'gtin' => '1234567890123',
            'estoque' => 100
        ];

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('obterUm')
            ->with($uuid)
            ->willReturn($expectedResult);

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->readOne($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertEquals($expectedResult, $data);
    }

    public function testReadOneComResultadoNulo()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'GET');
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('obterUm')
            ->with($uuid)
            ->willReturn([]);  // Return empty array instead of null to avoid presenter error

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->readOne($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertEquals([], $data);
    }

    public function testReadOneComException()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'GET');
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('obterUm')
            ->willThrowException(new Exception('Erro interno'));

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->readOne($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno', $data['msg']);
    }

    public function testUpdateComSucesso()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'PUT', [
            'nome' => 'Parafuso M8 Atualizado',
            'preco_custo' => 11.50,
            'estoque' => 150
        ]);
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $expectedResult = [
            'uuid' => $uuid,
            'nome' => 'Parafuso M8 Atualizado',
            'preco_custo' => 1150,
            'estoque' => 150
        ];

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('atualizar')
            ->with(
                $uuid,
                $this->callback(function($dados) use ($uuid) {
                    return $dados['uuid'] === $uuid &&
                           $dados['nome'] === 'Parafuso M8 Atualizado' &&
                           $dados['preco_custo'] === 1150 &&
                           $dados['estoque'] === 150;
                })
            )
            ->willReturn($expectedResult);

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->update($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertEquals($expectedResult, $data);
    }

    public function testUpdateComDomainException()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'PUT', [
            'nome' => 'Parafuso M8 Atualizado'
        ]);
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('atualizar')
            ->willThrowException(new \App\Exception\DomainHttpException('Material não encontrado', Response::HTTP_NOT_FOUND));

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->update($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Material não encontrado', $data['msg']);
    }

    public function testUpdateComException()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'PUT', [
            'nome' => 'Parafuso M8 Atualizado'
        ]);
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('atualizar')
            ->willThrowException(new Exception('Erro interno do servidor'));

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->update($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno do servidor', $data['msg']);
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('getFile', $data['meta']);
        $this->assertArrayHasKey('getLine', $data['meta']);
    }

    public function testUpdateComUuidInvalido()
    {
        // Arrange
        $request = Request::create('/material/invalid-uuid', 'PUT', [
            'nome' => 'Parafuso M8 Atualizado'
        ]);
        $request->setRouteResolver(function () {
            return new class {
                public function parameter($key) {
                    return 'invalid-uuid';
                }
            };
        });

        $controller = $this->createMock(MaterialController::class);
        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->update($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertStringContainsString('UUID válido', $data['msg']);
    }

    public function testDeleteComSucesso()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'DELETE');
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('deletar')
            ->with($uuid);

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->delete($request);

        // Assert
        $this->assertEquals(Response::HTTP_NO_CONTENT, $result->getStatusCode());
        $this->assertEmpty($result->getContent());
    }

    public function testDeleteComDomainException()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'DELETE');
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('deletar')
            ->willThrowException(new \App\Exception\DomainHttpException('Material não encontrado', Response::HTTP_NOT_FOUND));

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->delete($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Material não encontrado', $data['msg']);
    }

    public function testDeleteComException()
    {
        // Arrange
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $request = Request::create('/material/' . $uuid, 'DELETE');
        $request->setRouteResolver(function () use ($uuid) {
            return new class($uuid) {
                public function __construct(private string $uuid) {}
                public function parameter($key) {
                    return $this->uuid;
                }
            };
        });

        $controller = $this->createMock(MaterialController::class);
        $controller->expects($this->once())
            ->method('useRepositorio')
            ->willReturnSelf();
        $controller->expects($this->once())
            ->method('deletar')
            ->willThrowException(new Exception('Erro interno'));

        $presenter = new HttpJsonPresenter();
        $repositorio = $this->createMock(MaterialRepositorio::class);

        // Act
        $api = new MaterialApi($controller, $presenter, $repositorio);
        $result = $api->delete($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
        
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['err']);
        $this->assertEquals('Erro interno', $data['msg']);
    }
}