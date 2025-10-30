<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Infrastructure\Service\JsonWebTokenHandler\JsonWebTokenHandlerContract;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Usuario\RepositoryContract as UsuarioRepository;

class JsonWebTokenMiddleware
{
    public function __construct(
        public readonly JsonWebTokenHandlerContract $tokenHandler,
        public readonly UsuarioRepository $usuarioRepository,
    ) {}

    public function handle(Request $request, Closure $nextRequest)
    {
        $token = $request->bearerToken();

        $responseErr = [
            'err' => true,
            'msg' => 'É necessário identificação para acessar este recurso',
        ];

        if ($token === null) {
            return response()->json($responseErr, Response::HTTP_UNAUTHORIZED);
        }

        $dadosDoToken = $this->tokenHandler->decode($token);

        if ($dadosDoToken === null) {
            $responseErr['msg'] = 'Credenciais não aceitas! Solicite um novo token de autenticação.';
            return response()->json($responseErr, Response::HTTP_UNAUTHORIZED);
        }

        // Carrega o usuário na request
        $usuario = $this->usuarioRepository->findOneBy('uuid', $dadosDoToken['sub']);

        if ($usuario === null || sizeof($usuario) === 0 || !isset($usuario['uuid'])) {
            $responseErr['msg'] = 'É necessário autenticação para acessar este recurso';
            return response()->json($responseErr, Response::HTTP_UNAUTHORIZED);
        }

        // injeta usuário na request
        $request->attributes->set('user', $usuario);

        return $nextRequest($request);
    }
}
