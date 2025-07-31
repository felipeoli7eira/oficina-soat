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
