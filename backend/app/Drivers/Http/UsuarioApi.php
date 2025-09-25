<?php

declare(strict_types=1);

namespace App\Drivers\Http;

use App\Exception\DomainHttpException;
use App\Interface\Controlador\UsuarioControlador;
use App\Interface\Dto\Usuario\CriacaoDto as UsuarioCriacaoDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UsuarioApi
{
    public function __construct(public readonly UsuarioControlador $controlador) {}

    public function criar(Request $request)
    {
        try {
            $input = Validator::make($request->all(), [
                'nome'      => ['required', 'string'],
                'email'     => ['required', 'string'],
                'senha'     => ['required', 'string'],
                'documento' => ['required', 'string'],
            ]);

            if ($input->fails()) {
                return response()->json($input->errors(), Response::HTTP_BAD_REQUEST);
            }

            $resultado = $this->controlador->criar(new UsuarioCriacaoDto(
                nome: $input->validated()['nome'],
                email: $input->validated()['email'],
                senha: $input->validated()['senha'],
                documento: $input->validated()['documento'],
            ));

            $resultado->apresentar();
        } catch (DomainHttpException $err) {
            $resposta = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            return response()->json($resposta, $err->getCode());
        } catch (Throwable $err) {
            $resposta = [
                'err' => true,
                'msg' => $err->getMessage(),
            ];

            $codigo = Response::HTTP_INTERNAL_SERVER_ERROR;

            return response()->json($resposta, $codigo);
        }
    }

    public function listar(Request $request)
    {
        $resultado = $this->controlador->listar(
            porPagina: (int) $request->get('pp', 10),
            pagina: (int) $request->get('p', 1)
        );

        $resultado->apresentar();
    }
}
