<?php

declare(strict_types=1);

namespace App\Infrastructure\Web;

use App\Infrastructure\Service\JsonWebTokenHandler\JsonWebTokenHandlerContract;
use App\Domain\Usuario\Entity;
use App\Exception\DomainHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Interface\Controller\ClienteController;
use Throwable;

class ClienteWebController extends WebController
{
    public function __construct(public readonly ClienteController $clienteController) {}

    // public function create(Request $req)
    // {
    //     // validacoes basicas sem regra de negocio

    //     $validacao = Validator::make($req->only(['nome', 'email', 'senha', 'perfil']), [
    //         'nome'      => ['required', 'string'],
    //         'email'     => ['required', 'string', 'email'],
    //         'senha'     => ['required', 'string'],
    //         'perfil'    => ['required', 'string'],
    //     ]);

    //     $validacao->stopOnFirstFailure(true);

    //     if ($validacao->fails()) {
    //         return $this->errResponse($validacao->errors()->first(), 400);
    //     }

    //     $dados = $validacao->validated();

    //     $authenticatedUserUuid = $req->get('user')['uuid'];

    //     try {
    //         $data = $this->usuarioController->authenticatedUser($authenticatedUserUuid)->create(
    //             $dados['nome'],
    //             $dados['email'],
    //             $dados['senha'],
    //             $dados['perfil'],
    //         );
    //     } catch (DomainHttpException $err) {
    //         return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
    //     } catch (Throwable $err) {
    //         return $this->useException($err)->errResponse('Erro no procedimento', 500);
    //     }

    //     return $this->successResponse('Sucesso', 201, ['data' => $data]);
    // }

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

    // public function readOneByUuid(string $uuid, Request $req)
    // {
    //     // validacoes basicas sem regra de negocio

    //     $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
    //         'uuid' => ['required', 'string', 'uuid'],
    //     ]);

    //     $validacao->stopOnFirstFailure(true);

    //     if ($validacao->fails()) {
    //         return $this->errResponse($validacao->errors()->first(), 400);
    //     }

    //     try {
    //         $dados = $validacao->validated();

    //         $data = $this->usuarioController->readOneByUuid($dados['uuid']);
    //     } catch (DomainHttpException $err) {
    //         return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
    //     } catch (Throwable $err) {
    //         return $this->useException($err)->errResponse('Erro no procedimento', 500);
    //     }

    //     return $this->successResponse('Sucesso', 200, ['data' => $data]);
    // }

    // public function update(Request $req)
    // {
    //     // validacoes basicas sem regra de negocio

    //     $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid', 'nome', 'email', 'senha', 'perfil', 'ativo']), [
    //         'uuid'   => ['required', 'string', 'uuid'],
    //         'nome'   => ['nullable', 'string'],
    //         'email'  => ['nullable', 'string', 'email'],
    //         'senha'  => ['nullable', 'string'],
    //         'perfil' => ['nullable', 'string'],
    //         'ativo'  => ['nullable', 'boolean'],
    //     ]);

    //     $validacao->stopOnFirstFailure(true);

    //     if ($validacao->fails()) {
    //         return $this->errResponse($validacao->errors()->first(), 400);
    //     }

    //     try {
    //         $dados = $validacao->validated();

    //         $usuarioAutenticado = $req->get('user');

    //         $res = $this->usuarioController
    //             ->authenticatedUser($usuarioAutenticado['uuid'])
    //             ->update($dados['uuid'], $dados);
    //     } catch (DomainHttpException $err) {
    //         return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
    //     } catch (Throwable $err) {
    //         return $this->useException($err)->errResponse('Erro no procedimento', 500);
    //     }

    //     return $this->successResponse('Sucesso', 200, ['data' => $res]);
    // }

    // public function delete(Request $req)
    // {
    //     // validacoes basicas sem regra de negocio

    //     $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
    //         'uuid' => ['required', 'string', 'uuid'],
    //     ]);

    //     $validacao->stopOnFirstFailure(true);

    //     if ($validacao->fails()) {
    //         return $this->errResponse($validacao->errors()->first(), 400);
    //     }

    //     try {
    //         $dados = $validacao->validated();

    //         $authenticatedUserUuid = $req->get('user')['uuid'];

    //         $data = $this->usuarioController->authenticatedUser($authenticatedUserUuid)->delete($dados['uuid']);
    //     } catch (DomainHttpException $err) {
    //         return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
    //     } catch (Throwable $err) {
    //         return $this->useException($err)->errResponse('Erro no procedimento', 500);
    //     }

    //     if ($data === false) {
    //         return $this->errResponse('Erro no procedimento', 500);
    //     }

    //     return response()->noContent();
    // }

    // public function getAuthJwt(Request $req)
    // {
    //     // validacoes basicas sem regra de negocio

    //     $validacao = Validator::make($req->only(['email', 'senha']), [
    //         'email'     => ['required', 'string', 'email'],
    //         'senha'     => ['required', 'string'],
    //     ]);

    //     $validacao->stopOnFirstFailure(true);

    //     if ($validacao->fails()) {
    //         return $this->errResponse($validacao->errors()->first(), 400);
    //     }

    //     try {
    //         $dados = $validacao->validated();

    //         $token = $this->usuarioController->getAuthJwt($dados['email'], $dados['senha'], app(JsonWebTokenHandlerContract::class));
    //     } catch (DomainHttpException $err) {
    //         return $this->useException($err)->errResponse($err->getMessage(), $err->getCode());
    //     } catch (Throwable $err) {
    //         return $this->useException($err)->errResponse('Erro no procedimento', 500);
    //     }

    //     return $this->successResponse('Você está autenticado', 200, ['data' => ['token' => $token]]);
    // }
}
