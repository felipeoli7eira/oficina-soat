<?php

declare(strict_types=1);

namespace App\Http;

use App\Infrastructure\Controller\Ordem as OrdemController;
use App\Domain\Entity\Ordem\RepositorioInterface as OrdemRepositorio;

use App\Exception\DomainHttpException;
use App\Infrastructure\Presenter\HttpJsonPresenter;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OrdemApi
{
    public function __construct(
        public readonly OrdemController $controller,
        public readonly HttpJsonPresenter $presenter,
        public readonly OrdemRepositorio $repositorio,
    ) {}

    public function create(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['cliente_uuid', 'veiculo_uuid', 'descricao']), [
                'cliente_uuid' => ['required', 'string', 'uuid'],
                'veiculo_uuid' => ['required', 'string', 'uuid'],
                'descricao'    => ['nullable', 'string'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

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

    // public function castsUpdate(array $dados): array
    // {
    //     if (isset($dados['documento'])) {
    //         $dados['documento'] = str_replace(['.', '/', '-'], '', $dados['documento']);
    //     }

    //     if (isset($dados['fone'])) {
    //         $dados['fone'] = str_replace(['(', ')', '-', ' '], '', $dados['fone']);
    //     }

    //     return array_filter($dados, function (mixed $field) {
    //         return !is_null($field);
    //     });
    // }

    // public function read(Request $req)
    // {
    //     try {
    //         $res = $this->controller->useRepositorio($this->repositorio)->listar();
    //     } catch (DomainHttpException $err) {
    //         return response()->json([
    //             'err' => true,
    //             'msg' => $err->getMessage(),
    //         ], $err->getCode());
    //     } catch (Throwable $err) {
    //         return response()->json([
    //             'err' => true,
    //             'msg' => $err->getMessage(),
    //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($res);
    // }

    // public function readOne(Request $req)
    // {
    //     try {
    //         // validacao basica sem regras de negocio
    //         $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
    //             'uuid' => ['required', 'string', 'uuid'],
    //         ])->stopOnFirstFailure(true);

    //         if ($validacao->fails()) {
    //             throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
    //         }

    //         $dados = $validacao->validated();

    //         $res = $this->controller->useRepositorio($this->repositorio)->obterUm($dados['uuid']);
    //     } catch (DomainHttpException $err) {
    //         return response()->json([
    //             'err' => true,
    //             'msg' => $err->getMessage(),
    //         ], $err->getCode());
    //     } catch (Throwable $err) {
    //         return response()->json([
    //             'err' => true,
    //             'msg' => $err->getMessage(),
    //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     if (is_null($res)) {
    //         $this->presenter->setStatusCode(Response::HTTP_NOT_FOUND)->toPresent([]);
    //     }

    //     $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($res);
    // }

    // public function update(Request $req)
    // {
    //     try {
    //         // validacao basica sem regras de negocio
    //         $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid', 'nome', 'documento', 'email', 'fone']), [
    //             'uuid'      => ['required', 'string', 'uuid'],
    //             'nome'      => ['nullable', 'string'],
    //             'documento' => ['nullable', 'string'],
    //             'email'     => ['nullable', 'string', 'email'],
    //             'fone'      => ['nullable', 'string'],
    //         ])->stopOnFirstFailure(true);

    //         if ($validacao->fails()) {
    //             throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
    //         }

    //         $dados = $this->castsUpdate($validacao->validated());

    //         $responseSuccess = $this->controller->useRepositorio($this->repositorio)->atualizar($dados['uuid'], $dados);
    //     } catch (DomainHttpException $err) {
    //         $resErr = [
    //             'err' => true,
    //             'msg' => $err->getMessage(),
    //         ];

    //         return response()->json($resErr, $err->getCode());
    //     } catch (Throwable $err) {
    //         $resErr = [
    //             'err' => true,
    //             'msg' => $err->getMessage(),
    //             'meta' => [
    //                 'getFile' => $err->getFile(),
    //                 'getLine' => $err->getLine(),
    //             ]
    //         ];

    //         $cod = Response::HTTP_INTERNAL_SERVER_ERROR;

    //         return response()->json($resErr, $cod);
    //     }

    //     return $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($responseSuccess);
    // }

    // public function delete(Request $req)
    // {
    //     try {
    //         // validacao basica sem regras de negocio
    //         $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
    //             'uuid' => ['required', 'string', 'uuid'],
    //         ])->stopOnFirstFailure(true);

    //         if ($validacao->fails()) {
    //             throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
    //         }

    //         $dadosValidos = $validacao->validated();

    //         $this->controller->useRepositorio($this->repositorio)->deletar($dadosValidos['uuid']);
    //     } catch (DomainHttpException $err) {
    //         return response()->json([
    //             'err' => true,
    //             'msg' => $err->getMessage(),
    //         ], $err->getCode());
    //     } catch (Throwable $err) {
    //         return response()->json([
    //             'err' => true,
    //             'msg' => $err->getMessage(),
    //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     return response()->noContent();
    // }
}
