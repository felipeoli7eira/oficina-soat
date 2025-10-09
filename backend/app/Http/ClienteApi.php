<?php

declare(strict_types=1);

namespace App\Http;

use App\Infrastructure\Controller\Cliente as ClienteController;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;

use App\Exception\DomainHttpException;
use App\Infrastructure\Presenter\HttpJsonPresenter;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ClienteApi
{
    public function __construct(
        public readonly ClienteController $controller,
        public readonly HttpJsonPresenter $presenter,
        public readonly ClienteRepositorio $repositorio,
    ) {}

    public function create(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['nome', 'documento', 'email', 'fone']), [
                'nome'      => ['required', 'string'],
                'documento' => ['required', 'string'],
                'email'     => ['required', 'string', 'email'],
                'fone'      => ['required', 'string'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();
            // $dados = $this->castsCreate($validacao->validated());

            $res = $this->controller->useRepositorio($this->repositorio)->criar(
                $dados['nome'],
                $dados['documento'],
                $dados['email'],
                $dados['fone'],
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

    // public function castsCreate(array $dados): array
    // {
    //     if (is_null($dados['sku']) || (is_string($dados['sku']) && empty($dados['sku']))) {
    //         $dados['sku'] = null;
    //     }

    //     if (is_null($dados['descricao']) || (is_string($dados['descricao']) && empty($dados['descricao']))) {
    //         $dados['descricao'] = null;
    //     }

    //     if (is_string($dados['estoque'])) {
    //         $dados['estoque'] = intval($dados['estoque']);
    //     }

    //     if (is_string($dados['preco_custo'])) {
    //         $dados['preco_custo'] = intval($dados['preco_custo']);
    //     }

    //     $dados['preco_custo'] = (int) round($dados['preco_custo'] * 100);

    //     if (is_string($dados['preco_venda'])) {
    //         $dados['preco_venda'] = intval($dados['preco_venda']);
    //     }

    //     $dados['preco_venda'] = (int) round($dados['preco_venda'] * 100);

    //     if (is_string($dados['preco_uso_interno'])) {
    //         $dados['preco_uso_interno'] = intval($dados['preco_uso_interno']);
    //     }

    //     $dados['preco_uso_interno'] = (int) round($dados['preco_uso_interno'] * 100);

    //     return $dados;
    // }

    public function castsUpdate(array $dados): array
    {
        if (isset($dados['documento'])) {
            $dados['documento'] = str_replace(['.', '/', '-'], '', $dados['documento']);
        }

        if (isset($dados['fone'])) {
            $dados['fone'] = str_replace(['(', ')', '-', ' '], '', $dados['fone']);
        }

        return array_filter($dados, function (mixed $field) {
            return !is_null($field);
        });
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
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid', 'nome', 'documento', 'email', 'fone']), [
                'uuid'      => ['required', 'string', 'uuid'],
                'nome'      => ['nullable', 'string'],
                'documento' => ['nullable', 'string'],
                'email'     => ['nullable', 'string', 'email'],
                'fone'      => ['nullable', 'string'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $this->castsUpdate($validacao->validated());

            $responseSuccess = $this->controller->useRepositorio($this->repositorio)->atualizar($dados['uuid'], $dados);
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
