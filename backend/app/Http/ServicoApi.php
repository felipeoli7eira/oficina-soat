<?php

declare(strict_types=1);

namespace App\Http;

// use App\Domain\UseCase\Usuario\CreateUseCase;
// use App\Domain\UseCase\Usuario\UpdateUseCase;
// use App\Domain\UseCase\Usuario\DeleteUseCase;

use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepositorio;

use App\Exception\DomainHttpException;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use App\Infrastructure\Controller\Servico as ServicoController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ServicoApi
{
    public function __construct(
        public readonly ServicoController $controller,
        public readonly HttpJsonPresenter $presenter,
        public readonly ServicoRepositorio $repositorio,
    ) {}

    public function create(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['nome', 'valor']), [
                'nome'      => ['required', 'string'],
                'valor'     => ['required', 'numeric', 'decimal:0,2'], // recebido como valor em reais
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $res = $this->controller->useRepositorio($this->repositorio)->criar(
                $dados['nome'],
                (int) round($dados['valor'] * 100) // converte em centavos
            );
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

        $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent($res);
    }

    public function read(Request $req)
    {
        try {
            $res = $this->controller->useRepositorio($this->repositorio)->listar();
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

    public function readOne(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
                'uuid' => ['required', 'string', 'uuid'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $res = $this->controller->useRepositorio($this->repositorio)->obterUm($dados['uuid']);
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

        if (is_null($res)) {
            $this->presenter->setStatusCode(Response::HTTP_NOT_FOUND)->toPresent([]);
        }

        $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($res);
    }

    public function update(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['nome', 'uuid', 'valor']), [
                'uuid'  => ['required', 'string', 'uuid'],
                'nome'  => ['required', 'string'],
                'valor' => ['required', 'numeric', 'decimal:0,2']
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dadosValidados = $validacao->validated();

            $responseSuccess = $this->controller->useRepositorio($this->repositorio)->atualizar(
                $dadosValidados['uuid'],
                $dadosValidados['nome'],
                (int) round($dadosValidados['valor'] * 100) // converte em centavos
            );
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

        return $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($responseSuccess);
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

            $dadosValidos = $validacao->validated();

            $this->controller->useRepositorio($this->repositorio)->deletar($dadosValidos['uuid']);
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
