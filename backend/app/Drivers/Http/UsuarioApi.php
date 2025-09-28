<?php

declare(strict_types=1);

namespace App\Drivers\Http;

use App\Application\UseCase\Usuario\AtualizarUseCase;
use App\Application\UseCase\Usuario\CriarUseCase;
use App\Application\UseCase\Usuario\DeletarUseCase;
use App\Application\UseCase\Usuario\ListarUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use App\Infrastructure\Controller\Usuario as UsuarioController;
use App\Infrastructure\Dto\UsuarioDto;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UsuarioApi
{
    public function __construct(
        public readonly UsuarioController $controller,
        public readonly HttpJsonPresenter $presenter,
    ) {}

    public function criar(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['nome', 'email', 'senha']), [
                'nome'      => ['required', 'string'],
                'email'     => ['required', 'string', 'email'],
                'senha'     => ['required', 'string'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();
            $dto = new UsuarioDto(
                nome: $dados['nome'],
                email: $dados['email'],
                senha: $dados['senha'],
            );

            $res = $this->controller->criar($dto, app(CriarUseCase::class));

            $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent($res->toHttpResponse());
        } catch (DomainHttpException $err) {
            $res = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            return response()->json($res, $err->getCode());
        } catch (Throwable $err) {
            $res = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            $cod = Response::HTTP_INTERNAL_SERVER_ERROR;

            return response()->json($res, $cod);
        }
    }

    public function listar(Request $req)
    {
        try {
            $gateway = app(ListarUseCase::class);

            $res = $this->controller->listar($gateway);

            $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($res);
        } catch (DomainHttpException $err) {
            $res = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            return response()->json($res, $err->getCode());
        } catch (Throwable $err) {
            $res = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            $cod = Response::HTTP_INTERNAL_SERVER_ERROR;

            return response()->json($res, $cod);
        }
    }

    public function deletar(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
                'uuid' => ['required', 'string', 'uuid'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $uuid = $validacao->validated()['uuid'];

            $res = $this->controller->deletar($uuid, app(DeletarUseCase::class));
        } catch (DomainHttpException $err) {
            $res = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            return response()->json($res, $err->getCode());
        } catch (Throwable $err) {
            $res = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            $cod = Response::HTTP_INTERNAL_SERVER_ERROR;

            return response()->json($res, $cod);
        }

        return response()->noContent();
    }

    public function atualizar(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['nome', 'uuid']), [
                'uuid' => ['required', 'string', 'uuid'],
                'nome' => ['required', 'string'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dto = new UsuarioDto(
                nome: $validacao->validated()['nome'],
                uuid: $validacao->validated()['uuid'],
            );

            $responseSuccess = $this->controller->atualizar($dto, app(AtualizarUseCase::class));
        } catch (DomainHttpException $err) {
            $resErr = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            return response()->json($resErr, $err->getCode());
        } catch (Throwable $err) {
            $resErr = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            $cod = Response::HTTP_INTERNAL_SERVER_ERROR;

            return response()->json($resErr, $cod);
        }

        return $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($responseSuccess->toHttpResponse());
    }
}
