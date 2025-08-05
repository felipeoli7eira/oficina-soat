<?php

declare(strict_types=1);

namespace App\Modules\OS\Controller;

use App\Http\Controllers\Controller as BaseController;

use App\Modules\OS\Requests\AtualizacaoRequest;
use App\Modules\OS\Requests\CadastroRequest;
use App\Modules\OS\Requests\ObterUmPorUuidRequest;

use App\Modules\OS\Service\Service as OSService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class Controller extends BaseController
{
    public function __construct(private readonly OSService $service) {}

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

    public function atualizacao(AtualizacaoRequest $request)
    {
        try {
            $response = $this->service->atualizacao($request->uuid(), $request->toDto());
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
}
