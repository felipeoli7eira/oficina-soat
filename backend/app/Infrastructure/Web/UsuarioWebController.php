<?php

declare(strict_types=1);

namespace App\Infrastructure\Web;

use App\Exception\DomainHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Interface\Controller\UsuarioController;
use Throwable;

class UsuarioWebController extends WebController
{
    public function __construct(public readonly UsuarioController $usuarioController) {}

    public function create(Request $req)
    {
        // validacoes basicas sem regra de negocio

        $validacao = Validator::make($req->only(['nome', 'email', 'senha', 'perfil']), [
            'nome'      => ['required', 'string'],
            'email'     => ['required', 'string', 'email'],
            'senha'     => ['required', 'string'],
            'perfil'    => ['required', 'string'],
        ]);

        $validacao->stopOnFirstFailure(true);

        if ($validacao->fails()) {
            return $this->errResponse($validacao->errors()->first(), 400);
        }

        $dados = $validacao->validated();

        try {
            $data = $this->usuarioController->create(
                $dados['nome'],
                $dados['email'],
                $dados['senha'],
                $dados['perfil'],
            );
        } catch (DomainHttpException $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', $err->getCode());
        } catch (Throwable $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', 500);
        }

        return $this->successResponse('Sucesso', 201, ['data' => $data]);
    }

    public function read(Request $req)
    {
        try {
            $data = $this->usuarioController->read($req->all());
        } catch (DomainHttpException $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', $err->getCode());
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

            $data = $this->usuarioController->readOneByUuid($dados['uuid']);
        } catch (DomainHttpException $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', $err->getCode());
        } catch (Throwable $err) {
            return $this->useException($err)->errResponse('Erro no procedimento', 500);
        }

        return $this->successResponse('Sucesso', 200, ['data' => $data]);
    }

    public function update() {}

    public function delete() {}

    public function getAuthToken() {}

    public function destroyAuthToken() {}

    public function activate() {}

    public function deactivate() {}
}
