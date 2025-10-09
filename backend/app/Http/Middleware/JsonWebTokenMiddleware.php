<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Signature\TokenServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Entity\Usuario\RepositorioInterface as UsuarioRepositorio;

class JsonWebTokenMiddleware
{
    public function __construct(
        private TokenServiceInterface $tokenService,
        private UsuarioRepositorio $usuarioRepositorio,
    ) {}

    public function handle(Request $request, Closure $nextRequest)
    {
        $token = $request->bearerToken();

        $responseErr = [
            'err' => true,
            'msg' => 'Informe as credenciais de autenticação',
        ];

        if (! $token) {
            return response()->json($responseErr, Response::HTTP_UNAUTHORIZED);
        }

        $claims = $this->tokenService->validate($token);

        if ($claims === null) {
            $responseErr['msg'] = 'Token inválido';
            return response()->json($responseErr, Response::HTTP_UNAUTHORIZED);
        }

        // Carrega usuário
        $user = $this->usuarioRepositorio->encontrarPorIdentificadorUnico($claims->sub, 'uuid');

        if ($user === null) {
            $responseErr['msg'] = 'É necessário autenticação para acessar este recurso';
            return response()->json($responseErr, Response::HTTP_UNAUTHORIZED);
        }

        // injeta usuário na request
        $request->attributes->set('user', $user);

        return $nextRequest($request);
    }
}
