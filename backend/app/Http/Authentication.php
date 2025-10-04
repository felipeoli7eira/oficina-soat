<?php

declare(strict_types=1);

namespace App\Http;

use App\Domain\UseCase\Usuario\AuthenticateUseCase;
use Throwable;
use Illuminate\Http\Request;
use App\Exception\DomainHttpException;
use Illuminate\Support\Facades\Validator;
use App\Infrastructure\Controller\Usuario as UsuarioController;
use App\Infrastructure\Presenter\HttpJsonPresenter;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class Authentication
{
    public function __construct(
        public readonly UsuarioController $usuarioController,
        public readonly HttpJsonPresenter $presenter,
    ) {}

    public function authenticate(Request $req)
    {
        // validacao basica sem regras de negocio
        $validacao = Validator::make($req->only(['email', 'senha']), [
            'email' => ['required', 'string', 'email'],
            'senha' => ['required', 'string'],
        ])->stopOnFirstFailure(true);

        try {
            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $useCase = app(AuthenticateUseCase::class);

            $res = $this->usuarioController->authenticate(
                $validacao->validated()['email'],
                $validacao->validated()['senha'],
                $useCase
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

        $this->presenter->setStatusCode(Response::HTTP_OK)->toPresent($res->toAssociativeArray());
    }
}
