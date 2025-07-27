<?php

declare(strict_types=1);

namespace App\Modules\Cliente\Controller;

use App\Http\Controllers\Controller;
use App\Modules\Cliente\Dto\AtualizacaoDto;
use App\Modules\Cliente\Dto\CadastroDto;
use App\Modules\Cliente\Dto\ListagemDto;
use App\Modules\Cliente\Requests\AtualizacaoRequest;
use App\Modules\Cliente\Requests\CadastroRequest;
use App\Modules\Cliente\Requests\ListagemRequest;
use App\Modules\Cliente\Requests\ObterUmPorUuidRequest;

use App\Modules\Cliente\Service\Service as ClienteService;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class ClienteController extends Controller
{
    public function __construct(private readonly ClienteService $service) {}

    /**
     * @OA\Get(
     *      path="/api/cliente",
     *      tags={"Cliente"},
     *      summary="Cliente",
     *      description="Faz a listagem de clientes",
     *      @OA\Response(
     *          response=200,
     *          description="Para quando a requisição for bem sucedida. Pode ou não retornar clientes.",
     *       ),
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
