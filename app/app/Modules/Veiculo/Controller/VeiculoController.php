<?php

declare(strict_types=1);

namespace App\Modules\Veiculo\Controller;

use App\Http\Controllers\Controller;
use App\Modules\Veiculo\Dto\AtualizacaoDto;
use App\Modules\Veiculo\Dto\CadastroDto;
use App\Modules\Veiculo\Dto\ListagemDto;

use App\Modules\Veiculo\Service\Service as VeiculoService;

use App\Modules\Veiculo\Requests\AtualizacaoRequest;
use App\Modules\Veiculo\Requests\CadastroRequest;
use App\Modules\Veiculo\Requests\ObterUmPorUuidRequest;
use App\Modules\Veiculo\Requests\ListagemRequest;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class VeiculoController extends Controller
{
    public function __construct(private readonly VeiculoService $service) {}

    /**
     * @OA\Get(
     *      path="/api/veiculo",
     *      tags={"Veiculo"},
     *      summary="Listar veículos",
     *      description="Faz a listagem paginada de todos os veículos cadastrados, com filtro opcional por cliente",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="cliente_uuid",
     *          in="query",
     *          required=false,
     *          description="UUID do cliente para filtrar apenas os veículos dele",
     *          @OA\Schema(type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000")
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="Número da página para paginação",
     *          @OA\Schema(type="integer", minimum=1, example=1)
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          description="Quantidade de itens por página",
     *          @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Lista de veículos retornada com sucesso",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="uuid", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *                      @OA\Property(property="marca", type="string", example="Toyota"),
     *                      @OA\Property(property="modelo", type="string", example="Corolla"),
     *                      @OA\Property(property="placa", type="string", example="ABC-1234"),
     *                      @OA\Property(property="ano_fabricacao", type="integer", example=2020),
     *                      @OA\Property(property="cor", type="string", example="Prata"),
     *                      @OA\Property(property="chassi", type="string", example="1234567890ABCDEFG"),
     *                      @OA\Property(property="excluido", type="boolean", example=false),
     *                      @OA\Property(property="data_cadastro", type="string", format="date-time", example="2025-01-15T10:30:00.000000Z"),
     *                      @OA\Property(property="data_atualizacao", type="string", format="date-time", example=null, nullable=true),
     *                      @OA\Property(property="data_exclusao", type="string", format="date-time", example=null, nullable=true)
     *                  )
     *              ),
     *              @OA\Property(property="first_page_url", type="string", example="http://localhost:8080/api/veiculo?page=1"),
     *              @OA\Property(property="from", type="integer", example=1),
     *              @OA\Property(property="last_page", type="integer", example=5),
     *              @OA\Property(property="last_page_url", type="string", example="http://localhost:8080/api/veiculo?page=5"),
     *              @OA\Property(property="links", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="next_page_url", type="string", example="http://localhost:8080/api/veiculo?page=2", nullable=true),
     *              @OA\Property(property="path", type="string", example="http://localhost:8080/api/veiculo"),
     *              @OA\Property(property="per_page", type="integer", example=15),
     *              @OA\Property(property="prev_page_url", type="string", example=null, nullable=true),
     *              @OA\Property(property="to", type="integer", example=15),
     *              @OA\Property(property="total", type="integer", example=75)
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Erro interno do servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Erro interno do servidor")
     *          )
     *      ),
     *     )
     */
    public function listagem(ListagemRequest $request)
    {
        try {
            $dto = $request->toDto();
            $response = $this->service->listagem($dto);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }

    /**
     * @OA\Post(
     *      path="/api/veiculo",
     *      tags={"Veiculo"},
     *      summary="Cadastrar veículo",
     *      description="Cadastra um novo veículo",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"marca","modelo","ano","placa","chassi"},
     *              @OA\Property(property="marca", type="string", example="Toyota"),
     *              @OA\Property(property="modelo", type="string", example="Corolla"),
     *              @OA\Property(property="ano", type="integer", example=2020),
     *              @OA\Property(property="placa", type="string", example="ABC-1234"),
     *              @OA\Property(property="cor", type="string", example="Prata"),
     *              @OA\Property(property="chassi", type="string", example="1234567890ABCDEFG"),
     *              @OA\Property(property="cliente_uuid", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Veículo cadastrado com sucesso",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Dados de entrada inválidos",
     *      ),
     *     )
     */
    public function cadastro(CadastroRequest $request)
    {
        try {
            $dto = $request->toDto();
            $response = $this->service->cadastro($dto);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return Response::json($response, HttpResponse::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *      path="/api/veiculo/{uuid}",
     *      tags={"Veiculo"},
     *      summary="Obter veículo por UUID",
     *      description="Obtém um veículo específico pelo seu UUID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string", format="uuid")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Veículo encontrado com sucesso",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Veículo não encontrado",
     *      ),
     *     )
     */
    public function obterUmPorUuid(ObterUmPorUuidRequest $request)
    {
        try {
            $response = $this->service->obterUmPorUuid($request->uuid);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }

    /**
     * @OA\Delete(
     *      path="/api/veiculo/{uuid}",
     *      tags={"Veiculo"},
     *      summary="Remover veículo",
     *      description="Remove um veículo pelo seu UUID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="UUID do veículo a ser removido",
     *          @OA\Schema(type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Veículo removido com sucesso (sem conteúdo de retorno)",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Veículo não encontrado",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Veículo não encontrado")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Erro interno do servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Erro interno do servidor")
     *          )
     *      ),
     *     )
     */
    public function remocao(ObterUmPorUuidRequest $request)
    {
        try {
            $response = $this->service->remocao($request->uuid);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response, HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Put(
     *      path="/api/veiculo/{uuid}",
     *      tags={"Veiculo"},
     *      summary="Atualizar veículo",
     *      security={{"bearerAuth":{}}},
     *      description="Atualiza os dados de um veículo pelo seu UUID",
     *      @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string", format="uuid")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="marca", type="string", example="Toyota"),
     *              @OA\Property(property="modelo", type="string", example="Corolla"),
     *              @OA\Property(property="ano", type="integer", example=2021),
     *              @OA\Property(property="placa", type="string", example="DEF-5678"),
     *              @OA\Property(property="cor", type="string", example="Azul"),
     *              @OA\Property(property="chassi", type="string", example="0987654321ZYXWVUT"),
     *              @OA\Property(property="cliente_uuid", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Veículo atualizado com sucesso",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Veículo não encontrado",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Dados de entrada inválidos",
     *      ),
     *     )
     */
    public function atualizacao(AtualizacaoRequest $request)
    {
        try {
            $response = $this->service->atualizacao($request->route('uuid'), $request->toDto());
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }
}
