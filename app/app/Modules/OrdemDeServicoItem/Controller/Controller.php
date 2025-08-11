<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServicoItem\Controller;

use App\Http\Controllers\Controller as BaseController;

use App\Modules\OrdemDeServicoItem\Requests\AtualizacaoRequest;
use App\Modules\OrdemDeServicoItem\Requests\CadastroRequest;
use App\Modules\OrdemDeServicoItem\Requests\ObterUmPorUuidRequest;

use App\Modules\OrdemDeServicoItem\Service\Service as OSService;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;

use Throwable;
use DomainException;

class Controller extends BaseController
{
    public function __construct(private readonly OSService $service) {}

    /**
     * @OA\Get(
     *     path="/api/os-item",
     *     tags={"Item da OS"},
     *     summary="Faz a listagem dos itens de ordem de serviço cadastrados no sistema",
     *     security={{
     *       "bearerAuth":{}
     *      }},
     *     description="Retorna uma lista paginada de itens de ordem de serviço cadastrados no sistema.",
     *     @OA\Parameter(
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
     *         response=200,
     *         description="Para quando a requisição for bem-sucedida. Pode ou não retornar itens de ordem de serviço.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="uuid", type="string", format="uuid", example="2e895ab8-1183-4a03-94fd-005d68a7ebb2"),
     *                     @OA\Property(property="peca_insumo_uuid", type="string", format="uuid", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *                     @OA\Property(property="os_uuid", type="string", format="uuid", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *                     @OA\Property(property="observacao", type="string", example="Item adicional"),
     *                     @OA\Property(property="quantidade", type="integer", example=2),
     *                     @OA\Property(property="valor", type="number", format="float", example=150.50),
     *                     @OA\Property(property="data_cadastro", type="string", example="2025-08-05 18:28:07"),
     *                     @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null)
     *                 )
     *             ),
     *                          ),
             @OA\Property(property="first_page_url", type="string", example="http://localhost:8080/api/os-item?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=1),
     *             @OA\Property(property="last_page_url", type="string", example="http://localhost:8080/api/os-item?page=1"),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="label", type="string", example="&laquo; Anterior"),
     *                     @OA\Property(property="active", type="boolean", example=false)
     *                 )
     *             ),
     *             @OA\Property(property="next_page_url", type="string", nullable=true, example=null),
     *             @OA\Property(property="path", type="string", example="http://localhost:8080/api/os-item"),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *             @OA\Property(property="to", type="integer", example=7),
     *             @OA\Property(property="total", type="integer", example=7)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro inesperado no servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro interno do servidor.")
     *         )
     *     )
     * )
     */
    public function listagem()
    {
        try {
            $response = $this->service->listagem();
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
     *      path="/api/os-item",
     *      tags={"Item da OS"},
     *      summary="Cadastra um item de ordem de serviço",
     *      security={{"bearerAuth":{}}},
     *      description="Cadastra um item de ordem de serviço, dado os dados necessários.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"peca_insumo_uuid", "os_uuid", "quantidade", "valor"},
     *              @OA\Property(property="peca_insumo_uuid", type="string", format="uuid", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *              @OA\Property(property="os_uuid", type="string", format="uuid", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *              @OA\Property(property="observacao", type="string", example="Item adicional para o serviço"),
     *              @OA\Property(property="quantidade", type="integer", example=2),
     *              @OA\Property(property="valor", type="number", format="float", example=150.50)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Para quando a requisição for bem-sucedida. Retorna o cadastro do item de ordem de serviço.",
     *          @OA\JsonContent(
     *              @OA\Property(property="uuid", type="string", format="uuid", example="9b14c377-a173-443d-9f58-bd72eb3d2f60"),
     *              @OA\Property(property="peca_insumo_uuid", type="string", format="uuid", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *              @OA\Property(property="os_uuid", type="string", format="uuid", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *              @OA\Property(property="observacao", type="string", example="Item adicional para o serviço"),
     *              @OA\Property(property="quantidade", type="integer", example=2),
     *              @OA\Property(property="valor", type="number", format="float", example=150.50),
     *              @OA\Property(property="data_cadastro", type="string", format="date-time", example="2025-08-05 18:34:40"),
     *              @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null),
     *              @OA\Property(
     *                  property="pecaInsumo",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="uuid", type="string", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *                  @OA\Property(property="nome", type="string", example="Óleo do motor"),
     *                  @OA\Property(property="preco", type="number", format="float", example=75.25)
     *              ),
     *              @OA\Property(
     *                  property="ordemDeServico",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="uuid", type="string", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *                  @OA\Property(property="descricao", type="string", example="Troca de óleo"),
     *                  @OA\Property(property="valor_total", type="number", format="float", example=400.0)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Erro de validação no payload enviado.",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Dados enviados incorretamente"),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="string", example="O campo peca_insumo_uuid é obrigatório.")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Erro interno inesperado no servidor.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Erro interno do servidor.")
     *          )
     *      )
     * )
     */
    public function cadastro(CadastroRequest $request)
    {
        try {
            $response = $this->service->cadastro($request->toDto());
        } catch (DomainException $error) {
            $response = [
                'error'   => true,
                'message' => $error->getMessage()
            ];

            return Response::json($response, $error->getCode());
        } catch (Throwable $error) {
            $response = [
                'error'   => true,
                'message' => $error->getMessage()
            ];

            return Response::json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response, HttpResponse::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/os-item/{uuid}",
     *     summary="Obtém os dados de um item de ordem de serviço pelo uuid fornecido",
     *     description="Retorna as informações completas de um item de ordem de serviço com base no UUID fornecido.",
     *     tags={"Item da OS"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID do item de ordem de serviço que deseja consultar",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item de ordem de serviço encontrado com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="uuid", type="string", example="ce372925-6c2b-45ab-921e-85265d552324"),
     *             @OA\Property(property="peca_insumo_uuid", type="string", format="uuid", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *             @OA\Property(property="os_uuid", type="string", format="uuid", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *             @OA\Property(property="observacao", type="string", example="Item adicional para o serviço"),
     *             @OA\Property(property="quantidade", type="integer", example=2),
     *             @OA\Property(property="valor", type="number", format="float", example=150.50),
     *             @OA\Property(property="data_cadastro", type="string", example="2025-08-05 18:46:40"),
     *             @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null),
     *             @OA\Property(
     *                 property="pecaInsumo",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uuid", type="string", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *                 @OA\Property(property="nome", type="string", example="Óleo do motor"),
     *                 @OA\Property(property="preco", type="number", format="float", example=75.25)
     *             ),
     *             @OA\Property(
     *                 property="ordemDeServico",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uuid", type="string", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *                 @OA\Property(property="descricao", type="string", example="Troca de óleo"),
     *                 @OA\Property(property="valor_total", type="number", format="float", example=400.0)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item de ordem de serviço não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erros de validação"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(type="string", example="O campo uuid selecionado é inválido.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro não mapeado na aplicação ou no endpoint",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro interno do servidor.")
     *         )
     *     )
     * )
     */
    public function obterUmPorUuid(ObterUmPorUuidRequest $request)
    {
        try {
            $response = $this->service->obterUmPorUuid($request->uuid);
        } catch (ModelNotFoundException $th) {
            return Response::json([
                'error'   => true,
                'message' => 'Nenhum registro correspondente ao informado'
            ], HttpResponse::HTTP_NOT_FOUND);
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
     *     path="/api/os-item/{uuid}",
     *     summary="Remove um item de ordem de serviço",
     *     description="Remove um item de ordem de serviço com base no UUID informado.",
     *     tags={"Item da OS"},
     *      security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID do item de ordem de serviço a ser removido",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Item de ordem de serviço removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item de ordem de serviço não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erros de validação"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(type="string", example="O campo uuid selecionado é inválido.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno não tratado"
     *     )
     * )
     */
    public function remocao(ObterUmPorUuidRequest $request)
    {
        try {
            $response = $this->service->remocao($request->uuid);
        } catch (ModelNotFoundException $th) {
            return Response::json([
                'error'   => true,
                'message' => 'Nenhum registro correspondente ao informado'
            ], HttpResponse::HTTP_NOT_FOUND);
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
     *     path="/api/os-item/{uuid}",
     *     summary="Atualiza um item de ordem de serviço",
     *     description="Atualiza um item de ordem de serviço existente com base no UUID informado. É possível atualizar campos como peça/insumo, ordem de serviço, observação, quantidade e valor.",
     *     tags={"Item da OS"},
     *      security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID do item de ordem de serviço a ser atualizado",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="observacao", type="string", example="Observação atualizada"),
     *             @OA\Property(property="quantidade", type="integer", example=3),
     *             @OA\Property(property="valor", type="number", format="float", example=200.75)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item de ordem de serviço atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="uuid", type="string", format="uuid", example="4046dd88-642b-4208-a715-08466a28acda"),
     *             @OA\Property(property="peca_insumo_uuid", type="string", format="uuid", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *             @OA\Property(property="os_uuid", type="string", format="uuid", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *             @OA\Property(property="observacao", type="string", example="Observação atualizada"),
     *             @OA\Property(property="quantidade", type="integer", example=3),
     *             @OA\Property(property="valor", type="number", format="float", example=200.75),
     *             @OA\Property(property="pecaInsumo", type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="uuid", type="string", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *                 @OA\Property(property="nome", type="string", example="Filtro de óleo"),
     *                 @OA\Property(property="preco", type="number", format="float", example=65.90)
     *             ),
     *             @OA\Property(property="ordemDeServico", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uuid", type="string", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *                 @OA\Property(property="descricao", type="string", example="Troca de óleo completa"),
     *                 @OA\Property(property="valor_total", type="number", format="float", example=500.0)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos ou nenhum campo enviado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dados enviados incorretamente"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string", example="O campo quantidade deve ser pelo menos 1."))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item de ordem de serviço não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item de ordem de serviço não encontrado"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(type="string", example="O campo uuid selecionado é inválido.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno não tratado"
     *     )
     * )
     */
    public function atualizacao(AtualizacaoRequest $request)
    {
        try {
            $response = $this->service->atualizacao($request->uuid(), $request->toDto());
        } catch (ModelNotFoundException $th) {
            return Response::json([
                'error'   => true,
                'message' => 'Nenhum registro correspondente ao informado'
            ], HttpResponse::HTTP_NOT_FOUND);
        } catch (DomainException $error) {
            $response = [
                'error'   => true,
                'message' => $error->getMessage()
            ];

            return Response::json($response, $error->getCode());
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }
}
