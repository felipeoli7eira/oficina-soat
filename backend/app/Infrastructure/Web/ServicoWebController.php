<?php

declare(strict_types=1);

namespace App\Infrastructure\Web;

use App\Infrastructure\Service\JsonWebTokenHandler\JsonWebTokenHandlerContract;
use App\Domain\Usuario\Entity;
use App\Exception\DomainHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Interface\Controller\ServicoController;
use Throwable;

class ServicoWebController extends WebController
{
    public function __construct(public readonly ServicoController $servicoController) {}

    public function create(Request $req)
    {
        // validacoes basicas sem regra de negocio

        $validacao = Validator::make($req->only(['nome', 'valor', 'disponivel']), [
            'nome'       => ['required', 'string'],
            'valor'      => ['required', 'numeric'],
            'disponivel' => ['nullable', 'boolean'],
        ]);

        $validacao->stopOnFirstFailure(true);

        if ($validacao->fails()) {
            return $this->errResponse($validacao->errors()->first(), 400);
        }

        $dados = $validacao->validated();

        if (array_key_exists('disponivel', $dados) === false) {
            $dados['disponivel'] = false;
        }

        $authenticatedUserUuid = $req->get('user')['uuid'];

        try {
            $data = $this->servicoController->authenticatedUser($authenticatedUserUuid)->create(
                $dados['nome'],
                $dados['valor'],
                $dados['disponivel'],
            );
        } catch (DomainHttpException $err) {
            return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
        } catch (Throwable $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', 500);
        }

        return $this->successResponse('Sucesso', 201, ['data' => $data]);
    }

    public function read(Request $req)
    {
        try {
            $data = $this->clienteController->read($req->all());
        } catch (DomainHttpException $err) {
            return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
        } catch (Throwable $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', 500);
        }

        return $this->successResponse(msg: 'Sucesso', additionalData: ['data' => $data]);
    }

    public function readOneByUuid(string $uuid, Request $req)
    {
        // validacoes basicas sem regra de negocio

        $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
            'uuid' => ['required', 'string', 'uuid'],
        ]);

        $validacao->stopOnFirstFailure(true);

        if ($validacao->fails()) {
            return $this->errResponse($validacao->errors()->first(), 400);
        }

        try {
            $dados = $validacao->validated();

            $data = $this->clienteController->readOneByUuid($dados['uuid']);
        } catch (DomainHttpException $err) {
            return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
        } catch (Throwable $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', 500);
        }

        return $this->successResponse('Sucesso', 200, ['data' => $data]);
    }

    public function update(Request $req)
    {
        // validacoes basicas sem regra de negocio

        $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid', 'nome', 'email', 'documento', 'fone']), [
            'uuid'      => ['required', 'string', 'uuid'],
            'email'     => ['nullable', 'string', 'email'],
            'nome'      => ['nullable', 'string'],
            'documento' => ['nullable', 'string'],
            'fone'      => ['nullable', 'string'],
        ]);

        $validacao->stopOnFirstFailure(true);

        if ($validacao->fails()) {
            return $this->errResponse($validacao->errors()->first(), 400);
        }

        try {
            $dados = $validacao->validated();

            $usuarioAutenticado = $req->get('user');

            $res = $this->clienteController
                ->authenticatedUser($usuarioAutenticado['uuid'])
                ->update($dados['uuid'], $dados);
        } catch (DomainHttpException $err) {
            return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
        } catch (Throwable $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', 500);
        }

        return $this->successResponse('Sucesso', 200, ['data' => $res]);
    }

    public function delete(Request $req)
    {
        // validacoes basicas sem regra de negocio

        $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
            'uuid' => ['required', 'string', 'uuid'],
        ]);

        $validacao->stopOnFirstFailure(true);

        if ($validacao->fails()) {
            return $this->errResponse($validacao->errors()->first(), 400);
        }

        try {
            $dados = $validacao->validated();

            $authenticatedUserUuid = $req->get('user')['uuid'];

            $data = $this->clienteController
                ->authenticatedUser($authenticatedUserUuid)
                ->delete($dados['uuid']);
        } catch (DomainHttpException $err) {
            return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
        } catch (Throwable $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', 500);
        }

        if ($data === false) {
            return $this->errResponse('Erro no procedimento', 500);
        }

        return response()->noContent();
    }
}
