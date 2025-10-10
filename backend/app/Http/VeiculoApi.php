<?php

declare(strict_types=1);

namespace App\Http;

use App\Infrastructure\Controller\Veiculo as VeiculoController;
use App\Domain\Entity\Veiculo\RepositorioInterface as VeiculoRepositorio;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;

use App\Exception\DomainHttpException;
use App\Infrastructure\Presenter\HttpJsonPresenter;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class VeiculoApi
{
    public function __construct(
        public readonly VeiculoController $controller,
        public readonly HttpJsonPresenter $presenter,
        public readonly VeiculoRepositorio $repositorio,
        public readonly ClienteRepositorio $clienteRepositorio,
    ) {}

    public function create(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['marca', 'modelo', 'placa', 'ano', 'cliente_uuid']), [
                'marca'        => ['required', 'string'],
                'modelo'       => ['required', 'string'],
                'placa'        => ['required', 'string'],
                'ano'          => ['required', 'integer'],
                'cliente_uuid' => ['required', 'string', 'uuid'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $res = $this->controller->useRepositorio($this->repositorio)->useClienteRepositorio($this->clienteRepositorio)
                ->criar(
                    $dados['marca'],
                    $dados['modelo'],
                    $dados['placa'],
                    $dados['ano'],
                    $dados['cliente_uuid'],
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
                'meta' => [
                    'getFile' => $err->getFile(),
                    'getLine' => $err->getLine(),
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent($res);
    }

    public function castsUpdate(array $dados): array
    {
        return array_filter($dados, function (mixed $field) {
            return !is_null($field);
        });
    }

    public function read(Request $req)
    {
        try {
            $res = $this->controller
                ->useRepositorio($this->repositorio)
                ->useClienteRepositorio($this->clienteRepositorio)
                ->listar();
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

            $res = $this->controller
                ->useRepositorio($this->repositorio)
                ->useClienteRepositorio($this->clienteRepositorio)
                ->obterUm($dados['uuid']);
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
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid', 'marca', 'modelo', 'placa', 'ano']), [
                'uuid'         => ['required', 'string', 'uuid'],
                'marca'        => ['nullable', 'string'],
                'modelo'       => ['nullable', 'string'],
                'placa'        => ['nullable', 'string'],
                'ano'          => ['nullable', 'integer'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $this->castsUpdate($validacao->validated());

            $responseSuccess = $this->controller
                ->useRepositorio($this->repositorio)
                ->useClienteRepositorio($this->clienteRepositorio)
                ->atualizar($dados['uuid'], $dados);
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
                'meta' => [
                    'getFile' => $err->getFile(),
                    'getLine' => $err->getLine(),
                ]
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
