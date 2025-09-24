<?php

declare(strict_types=1);

namespace App\Drivers\Http;

use App\Interface\Controlador\UsuarioControlador;
use App\Interface\Dto\Usuario\CriacaoDto as UsuarioCriacaoDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UsuarioApi
{
    public function __construct(public readonly UsuarioControlador $controlador) {}

    public function criar(Request $request)
    {
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
    }

    public function listar(Request $request)
    {
        // $input = Validator::make($request->all(), [
        //     'nome'      => ['required', 'string'],
        //     'email'     => ['required', 'string'],
        //     'senha'     => ['required', 'string'],
        //     'documento' => ['required', 'string'],
        // ]);

        // if ($input->fails()) {
        //     return response()->json($input->errors(), Response::HTTP_BAD_REQUEST);
        // }

        $resultado = $this->controlador->listar(
            porPagina: $request->get('porPagina', 10),
            pagina: $request->get('pagina', 1)
        );

        $resultado->apresentar();
    }
}
