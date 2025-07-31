<?php

declare(strict_types=1);

namespace App\Modules\Usuario\Controller;

use App\Http\Controllers\Controller as BaseController;

use App\Modules\Usuario\Dto\ListagemDto;

use App\Modules\Usuario\Requests\AtualizacaoRequest;
use App\Modules\Usuario\Requests\CadastroRequest;
use App\Modules\Usuario\Requests\ObterUmPorUuidRequest;

use App\Modules\Usuario\Service\Service as UsuarioService;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class Controller extends BaseController
{
    public function __construct(private readonly UsuarioService $service) {}

    /**
     * @OA\Get(
     *     path="/api/usuario",
     *     tags={"Usuario"},
     *     summary="Faz a listagem dos usuários cadastrados",
     *     description="Retorna uma lista paginada de usuários cadastrados no sistema.",
     *     @OA\Response(
     *         response=200,
     *         description="Para quando a requisição for bem-sucedida. Pode ou não retornar usuários.",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=17),
     *                     @OA\Property(property="uuid", type="string", format="uuid", example="b7466bb6-0d93-4f32-a4a0-f5cad13e3360"),
     *                     @OA\Property(property="nome", type="string", example="Felipe"),
     *                     @OA\Property(property="role_id", type="integer", example=3),
     *                     @OA\Property(property="status", type="string", example="ativo"),
     *                     @OA\Property(property="excluido", type="boolean", example=false),
     *                     @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *                     @OA\Property(property="data_cadastro", type="string", example="31/07/2025 19:33"),
     *                     @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null)
     *                 )
     *             ),
     *             @OA\Property(property="first_page_url", type="string", example="http://localhost:8080/api/usuario?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=1),
     *             @OA\Property(property="last_page_url", type="string", example="http://localhost:8080/api/usuario?page=1"),
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
     *             @OA\Property(property="path", type="string", example="http://localhost:8080/api/usuario"),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *             @OA\Property(property="to", type="integer", example=4),
     *             @OA\Property(property="total", type="integer", example=4)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro inesperado no servidor"
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
     *      path="/api/usuario",
     *      tags={"Usuario"},
     *      summary="Cadastra um usuário",
     *      description="Cadastra um usuário",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"nome", "papel", "status", "cep"},
     *              @OA\Property(property="nome", type="string", example="Jacinto Pinto"),
     *              @OA\Property(property="papel", type="string", example="mecanico || comercial || gestor_estoque || atendente"),
     *              @OA\Property(property="status", type="string", example="ativo || inativo"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Para quando a requisição for bem-sucedida. Retorna o cadastro do usuário.",
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Para quando a requisição falhar por erros nos dados.",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Para quando houver algum erro não mapeado na aplicação de forma geral ou no endpoint.",
     *      ),
     * )
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
     *     path="/api/usuario/{uuid}",
     *     summary="Obtém os dados de um usuário pelo uuid",
     *     description="Retorna os dados completos de um usuário específico com base no UUID informado.",
     *     tags={"Usuario"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID do usuário que deseja consultar",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=17),
     *             @OA\Property(property="uuid", type="string", format="uuid", example="b7466bb6-0d93-4f32-a4a0-f5cad13e3360"),
     *             @OA\Property(property="nome", type="string", example="Felipe"),
     *             @OA\Property(property="role_id", type="integer", example=3),
     *             @OA\Property(property="status", type="string", example="ativo"),
     *             @OA\Property(property="excluido", type="boolean", example=false),
     *             @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *             @OA\Property(property="data_cadastro", type="string", example="31/07/2025 19:33"),
     *             @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null),
     *             @OA\Property(
     *                 property="role",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="mecanico"),
     *                 @OA\Property(property="guard_name", type="string", example="web"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-31T18:29:09.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-31T18:29:09.000000Z")
     *             )
     *         )
     *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Usuário não encontrado",
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
     *         description="Erro não mapeado na aplicação ou no endpoint"
     *     )
     * )
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
     *     path="/api/usuario/{uuid}",
     *     summary="Remove um usuário",
     *     description="Remove um usuário com base no UUID informado.",
     *     tags={"Usuario"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID do usuário a ser removido",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Usuário removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
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
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response, HttpResponse::HTTP_NO_CONTENT);
    }

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

        return Response::json($response, HttpResponse::HTTP_NO_CONTENT);
    }
}
