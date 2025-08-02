<?php

declare(strict_types=1);

namespace App\Modules\PecaInsumo\Controller;

use App\Http\Controllers\Controller;
use App\Modules\PecaInsumo\Dto\AtualizacaoDto;
use App\Modules\PecaInsumo\Dto\CadastroDto;
use App\Modules\PecaInsumo\Dto\ListagemDto;

use App\Modules\PecaInsumo\Service\Service as PecaInsumoService;

use App\Modules\PecaInsumo\Requests\AtualizacaoRequest;
use App\Modules\PecaInsumo\Requests\CadastroRequest;
use App\Modules\PecaInsumo\Requests\ObterUmPorIdRequest;
use App\Modules\PecaInsumo\Requests\ListagemRequest;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class PecaInsumoController extends Controller
{
    public function __construct(private readonly PecaInsumoService $service) {}

    /**
     * @OA\Get(
     *      path="/api/peca_insumo",
     *      tags={"PecaInsumo"},
     *      summary="Listar peças e insumos",
     *      description="Faz a listagem paginada de todas as peças e insumos cadastrados",
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
     *          description="Lista de peças e insumos retornada com sucesso",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="gtin", type="string", example="7891234567890"),
     *                      @OA\Property(property="descricao", type="string", example="Filtro de óleo"),
     *                      @OA\Property(property="valor_custo", type="number", format="float", example=25.50),
     *                      @OA\Property(property="valor_venda", type="number", format="float", example=45.90),
     *                      @OA\Property(property="qtd_atual", type="integer", example=100),
     *                      @OA\Property(property="qtd_segregada", type="integer", example=5),
     *                      @OA\Property(property="status", type="string", example="ativo"),
     *                      @OA\Property(property="excluido", type="boolean", example=false),
     *                      @OA\Property(property="data_cadastro", type="string", format="date-time", example="2025-01-15T10:30:00.000000Z"),
     *                      @OA\Property(property="data_atualizacao", type="string", format="date-time", example=null, nullable=true),
     *                      @OA\Property(property="data_exclusao", type="string", format="date-time", example=null, nullable=true)
     *                  )
     *              ),
     *              @OA\Property(property="first_page_url", type="string", example="http://localhost:8080/api/peca_insumo?page=1"),
     *              @OA\Property(property="from", type="integer", example=1),
     *              @OA\Property(property="last_page", type="integer", example=5),
     *              @OA\Property(property="last_page_url", type="string", example="http://localhost:8080/api/peca_insumo?page=5"),
     *              @OA\Property(property="links", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="next_page_url", type="string", example="http://localhost:8080/api/peca_insumo?page=2", nullable=true),
     *              @OA\Property(property="path", type="string", example="http://localhost:8080/api/peca_insumo"),
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
            $dto = new ListagemDto();
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
     *      path="/api/peca_insumo",
     *      tags={"PecaInsumo"},
     *      summary="Cadastrar peça ou insumo",
     *      description="Cadastra uma nova peça ou insumo",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"gtin","descricao","valor_custo","valor_venda","qtd_atual"},
     *              @OA\Property(property="gtin", type="string", example="7891234567890"),
     *              @OA\Property(property="descricao", type="string", example="Filtro de óleo"),
     *              @OA\Property(property="valor_custo", type="number", format="float", example=25.50),
     *              @OA\Property(property="valor_venda", type="number", format="float", example=45.90),
     *              @OA\Property(property="qtd_atual", type="integer", example=100),
     *              @OA\Property(property="qtd_segregada", type="integer", example=0),
     *              @OA\Property(property="status", type="string", example="ativo"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Peça ou insumo cadastrado com sucesso",
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
     *      path="/api/peca_insumo/{id}",
     *      tags={"PecaInsumo"},
     *      summary="Obter peça ou insumo por ID",
     *      description="Obtém uma peça ou insumo específico pelo seu ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Peça ou insumo encontrado com sucesso",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Peça ou insumo não encontrado",
     *      ),
     *     )
     */
    public function obterUmPorId(ObterUmPorIdRequest $request)
    {
        try {
            $response = $this->service->obterUmPorId($request->id);
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
     *      path="/api/peca_insumo/{id}",
     *      tags={"PecaInsumo"},
     *      summary="Remover peça ou insumo",
     *      description="Remove uma peça ou insumo pelo seu ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID da peça ou insumo a ser removido",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Peça ou insumo removido com sucesso (sem conteúdo de retorno)",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Peça ou insumo não encontrado",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Peça ou insumo não encontrado")
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
    public function remocao(ObterUmPorIdRequest $request)
    {
        try {
            $response = $this->service->remocao($request->id);
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
     *      path="/api/peca_insumo/{id}",
     *      tags={"PecaInsumo"},
     *      summary="Atualizar peça ou insumo",
     *      description="Atualiza os dados de uma peça ou insumo pelo seu ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="gtin", type="string", example="7891234567890"),
     *              @OA\Property(property="descricao", type="string", example="Filtro de óleo atualizado"),
     *              @OA\Property(property="valor_custo", type="number", format="float", example=30.00),
     *              @OA\Property(property="valor_venda", type="number", format="float", example=50.00),
     *              @OA\Property(property="qtd_atual", type="integer", example=85),
     *              @OA\Property(property="qtd_segregada", type="integer", example=3),
     *              @OA\Property(property="status", type="string", example="ativo"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Peça ou insumo atualizado com sucesso",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Peça ou insumo não encontrado",
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
            $response = $this->service->atualizacao($request->route('id'), $request->toDto());
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }
}
