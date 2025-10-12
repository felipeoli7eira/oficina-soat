<?php

declare(strict_types=1);

namespace App\Http;

use App\Infrastructure\Controller\Ordem as OrdemController;

use App\Domain\Entity\Ordem\RepositorioInterface as OrdemRepositorio;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;
use App\Domain\Entity\Veiculo\RepositorioInterface as VeiculoRepositorio;
use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepositorio;
use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;

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
        public readonly ClienteRepositorio $clienteRepositorio,
        public readonly VeiculoRepositorio $veiculoRepositorio,
        public readonly ServicoRepositorio $servicoRepositorio,
        public readonly MaterialRepositorio $materialRepositorio,
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

            $res = $this->controller
                ->useRepositorio($this->repositorio)
                ->useClienteRepositorio($this->clienteRepositorio)
                ->useVeiculoRepositorio($this->veiculoRepositorio)
                ->criar(
                    $dados['cliente_uuid'],
                    $dados['veiculo_uuid'],
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
                'meta' => [
                    'getFile' => $err->getFile(),
                    'getLine' => $err->getLine(),
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent($res);
    }

    public function read(Request $req)
    {
        try {
            $res = $this->controller
                ->useRepositorio($this->repositorio)
                ->useClienteRepositorio($this->clienteRepositorio)
                ->useVeiculoRepositorio($this->veiculoRepositorio)
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
                ->useVeiculoRepositorio($this->veiculoRepositorio)
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
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid', 'descricao']), [
                'uuid'      => ['required', 'string', 'uuid'],
                'descricao' => ['nullable', 'string'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            if (! isset($dados['descricao']) || (is_string($dados['descricao']) && empty($dados['descricao']))) {
                return response()->json([
                    'err' => false,
                    'msg' => 'Nada para atualizar',
                ]);
            }

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

    public function updateStatus(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid', 'status']), [
                'uuid'      => ['required', 'string', 'uuid'],
                'status'    => ['required', 'string'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $responseSuccess = $this->controller->useRepositorio($this->repositorio)->atualizarStatus($dados['uuid'], $dados['status']);
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

    public function addService(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['ordem_uuid', 'servico_uuid']), [
                'ordem_uuid'   => ['required', 'string', 'uuid'],
                'servico_uuid' => ['required', 'string', 'uuid'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $res = $this->controller
                ->useRepositorio($this->repositorio)
                ->useServicoRepositorio($this->servicoRepositorio)
                ->adicionaServico(
                    $dados['ordem_uuid'],
                    $dados['servico_uuid'],
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

        $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent(['uuid' => $res]);
    }

    public function removeService(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['ordem_uuid', 'servico_uuid']), [
                'ordem_uuid'   => ['required', 'string', 'uuid'],
                'servico_uuid' => ['required', 'string', 'uuid'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $res = $this->controller
                ->useRepositorio($this->repositorio)
                ->useServicoRepositorio($this->servicoRepositorio)
                ->removeServico(
                    $dados['ordem_uuid'],
                    $dados['servico_uuid'],
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

        $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent(['success' => $res]);
    }

    public function addMaterial(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['ordem_uuid', 'material_uuid']), [
                'ordem_uuid'   => ['required', 'string', 'uuid'],
                'material_uuid' => ['required', 'string', 'uuid'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $res = $this->controller
                ->useRepositorio($this->repositorio)
                ->useMaterialRepositorio($this->materialRepositorio)
                ->adicionaMaterial(
                    $dados['ordem_uuid'],
                    $dados['material_uuid'],
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

        $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent(['uuid' => $res]);
    }

    public function removeMaterial(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->only(['ordem_uuid', 'material_uuid']), [
                'ordem_uuid'    => ['required', 'string', 'uuid'],
                'material_uuid' => ['required', 'string', 'uuid'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $res = $this->controller
                ->useRepositorio($this->repositorio)
                ->useMaterialRepositorio($this->materialRepositorio)
                ->removeMaterial(
                    $dados['ordem_uuid'],
                    $dados['material_uuid'],
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

        $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent(['success' => $res]);
    }
}
