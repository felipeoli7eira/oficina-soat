<?php

declare(strict_types=1);

namespace App\Drivers\Http;

use App\Application\UseCase\Usuario\CreateUseCase;
use App\Application\UseCase\Usuario\ReadUseCase;
use App\Application\UseCase\Usuario\UpdateUseCase;
use App\Application\UseCase\Usuario\DeleteUseCase;

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

    public function create(Request $req)
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

            $res = $this->controller->criar($dto, app(CreateUseCase::class));
        } catch (DomainHttpException $err) {
            return response()->json([
                'err' => true,
                'msg' => $err->getMessage(),
            ], $err->getCode());
        } catch (Throwable $err) {
            return response()->json([
                'err' => true,
                'msg' => $err->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent($res->toHttpResponse());
    }

    public function read(Request $req)
    {
        try {
            $gateway = app(ReadUseCase::class);

            $res = $this->controller->listar($gateway);
        } catch (DomainHttpException $err) {
            return response()->json([
                'err' => true,
                'msg' => $err->getMessage(),
            ], $err->getCode());
        } catch (Throwable $err) {
            return response()->json([
                'err' => true,
                'msg' => $err->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($res);
    }

    public function update(Request $req)
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

            $responseSuccess = $this->controller->atualizar($dto, app(UpdateUseCase::class));
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

    public function delete(Request $req)
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

            $this->controller->deletar($uuid, app(DeleteUseCase::class));
        } catch (DomainHttpException $err) {
            return response()->json([
                'err' => true,
                'msg' => $err->getMessage(),
            ], $err->getCode());
        } catch (Throwable $err) {
            return response()->json([
                'err' => true,
                'msg' => $err->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->noContent();
    }
}
