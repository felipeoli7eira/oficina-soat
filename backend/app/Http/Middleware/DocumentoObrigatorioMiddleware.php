<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;

class DocumentoObrigatorioMiddleware
{
    public function __construct(
        private ClienteRepositorio $clienteRepositorio,
    ) {}

    public function handle(Request $request, Closure $nextRequest)
    {
        $documento = $request->header('X-App-Document');

        $responseErr = [
            'err' => true,
            'msg' => 'Informe o documento para continuar com esta operação.',
        ];

        if (! $documento) {
            return response()->json($responseErr, Response::HTTP_UNAUTHORIZED);
        }

        // Carrega cliente
        $cliente = $this->clienteRepositorio->encontrarPorIdentificadorUnico($documento, 'documento');

        if ($cliente === null) {
            $responseErr['msg'] = 'Cliente não encontrado.';
            return response()->json($responseErr, Response::HTTP_UNAUTHORIZED);
        }

        // injeta dados do cliente na request
        $request->attributes->set('cliente_documento', $cliente->documento);
        $request->attributes->set('cliente_uuid', $cliente->uuid);

        return $nextRequest($request);
    }
}
