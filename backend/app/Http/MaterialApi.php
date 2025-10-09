<?php

declare(strict_types=1);

namespace App\Http;

use App\Infrastructure\Controller\Material as MaterialController;
use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;

use App\Exception\DomainHttpException;
use App\Infrastructure\Presenter\HttpJsonPresenter;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MaterialApi
{
    public function __construct(
        public readonly MaterialController $controller,
        public readonly HttpJsonPresenter $presenter,
        public readonly MaterialRepositorio $repositorio,
    ) {}

    public function create(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['nome', 'gtin', 'estoque', 'preco_custo', 'preco_venda', 'preco_uso_interno', 'sku', 'descricao']), [
                'nome'              => ['required', 'string'],
                'gtin'              => ['required', 'string'],
                'estoque'           => ['required', 'integer'],
                'preco_custo'       => ['required', 'decimal:0,2'],
                'preco_venda'       => ['required', 'decimal:0,2'],
                'preco_uso_interno' => ['required', 'decimal:0,2'],
                'sku'               => ['string', 'nullable'],
                'descricao'         => ['string', 'nullable'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $this->castsCreate($validacao->validated());

            $res = $this->controller->useRepositorio($this->repositorio)->criar(
                $dados['nome'],
                $dados['gtin'],
                $dados['estoque'],
                $dados['preco_custo'],
                $dados['preco_venda'],
                $dados['preco_uso_interno'],
                $dados['sku'],
                $dados['descricao'],
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

    public function castsCreate(array $dados): array
    {
        if (is_null($dados['sku']) || (is_string($dados['sku']) && empty($dados['sku']))) {
            $dados['sku'] = null;
        }

        if (is_null($dados['descricao']) || (is_string($dados['descricao']) && empty($dados['descricao']))) {
            $dados['descricao'] = null;
        }

        if (is_string($dados['estoque'])) {
            $dados['estoque'] = intval($dados['estoque']);
        }

        if (is_string($dados['preco_custo'])) {
            $dados['preco_custo'] = intval($dados['preco_custo']);
        }

        $dados['preco_custo'] = (int) round($dados['preco_custo'] * 100);

        if (is_string($dados['preco_venda'])) {
            $dados['preco_venda'] = intval($dados['preco_venda']);
        }

        $dados['preco_venda'] = (int) round($dados['preco_venda'] * 100);

        if (is_string($dados['preco_uso_interno'])) {
            $dados['preco_uso_interno'] = intval($dados['preco_uso_interno']);
        }

        $dados['preco_uso_interno'] = (int) round($dados['preco_uso_interno'] * 100);

        return $dados;
    }

    public function castsUpdate(array $dados): array
    {
        if (! isset($dados['sku']) || (is_string($dados['sku']) && empty($dados['sku']))) {
            $dados['sku'] = null;
        }

        if (! isset($dados['descricao']) || (is_string($dados['descricao']) && empty($dados['descricao']))) {
            $dados['descricao'] = null;
        }

        if (isset($dados['estoque']) && is_string($dados['estoque'])) {
            $dados['estoque'] = intval($dados['estoque']);
        }

        if (isset($dados['preco_custo'])) {
            if (is_string($dados['preco_custo'])) {
                $dados['preco_custo'] = intval($dados['preco_custo']);
            }

            $dados['preco_custo'] = (int) round($dados['preco_custo'] * 100);
        }

        if (isset($dados['preco_venda'])) {
            if (is_string($dados['preco_venda'])) {
                $dados['preco_venda'] = intval($dados['preco_venda']);
            }

            $dados['preco_venda'] = (int) round($dados['preco_venda'] * 100);
        }

        if (isset($dados['preco_uso_interno'])) {
            if (is_string($dados['preco_uso_interno'])) {
                $dados['preco_uso_interno'] = intval($dados['preco_uso_interno']);
            }

            $dados['preco_uso_interno'] = (int) round($dados['preco_uso_interno'] * 100);
        }

        return array_filter($dados, fn($field) => ! is_null($field));
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
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid', 'nome', 'gtin', 'estoque', 'preco_custo', 'preco_venda', 'preco_uso_interno', 'sku', 'descricao']), [
                'uuid'              => ['nullable', 'string', 'uuid'],
                'nome'              => ['nullable', 'string'],
                'gtin'              => ['nullable', 'string'],
                'estoque'           => ['nullable', 'integer'],
                'preco_custo'       => ['nullable', 'decimal:0,2'],
                'preco_venda'       => ['nullable', 'decimal:0,2'],
                'preco_uso_interno' => ['nullable', 'decimal:0,2'],
                'sku'               => ['string', 'nullable'],
                'descricao'         => ['string', 'nullable'],
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
