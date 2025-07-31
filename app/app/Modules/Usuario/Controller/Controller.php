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

    public function listagem()
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

        return Response::json($response);
    }
}
