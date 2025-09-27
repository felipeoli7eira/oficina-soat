<?php

declare(strict_types=1);

namespace App\Drivers\Http;

use App\Application\UseCase\Usuario\CriarUsuarioUseCase;
use App\Exception\DomainHttpException;
use App\Interface\Presenter\HttpJsonPresenter;
use App\Interface\Controller\Usuario as UsuarioController;
use App\Interface\Dto\UsuarioDto;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UsuarioApi
{
    public function __construct(
        public readonly UsuarioController $controller,
        public readonly HttpJsonPresenter $presenter,
    ) {}

    public function criar(Request $request)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($request->only(['nome', 'email', 'senha']), [
                'nome'      => ['required', 'string'],
                'email'     => ['required', 'string', 'email'],
                'senha'     => ['required', 'string'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();
            $dto = new UsuarioDto(
                nome: $dados['nome'],
                email: $dados['email'],
                senha: $dados['senha'],
            );

            $res = $this->controller->criar($dto, app(CriarUsuarioUseCase::class));

            $this->presenter->setStatusCode(Response::HTTP_CREATED)->toPresent($res->toHttpResponse());
        } catch (DomainHttpException $err) {
            $res = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            return response()->json($res, $err->getCode());
        } catch (Throwable $err) {
            $res = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            $cod = Response::HTTP_INTERNAL_SERVER_ERROR;

            return response()->json($res, $cod);
        }
    }
}
