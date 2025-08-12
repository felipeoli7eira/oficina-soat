<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Modules\Auth\Requests\AuthUsuarioRequest;
use App\Modules\Auth\Services\AuthUsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

class AuthUsuarioController extends BaseController
{
    public function __construct(public readonly AuthUsuarioService $service) {}

    /**
     * @OA\Post(
     *     path="/api/usuario/auth/autenticar",
     *     summary="Autenticar usuário",
     *     description="Possibilita a autenticação de um usuário do sistema",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "senha"},
     *             @OA\Property(property="email", type="string", example="atendente@example.com"),
     *             @OA\Property(property="senha", type="string", example="senha8caracteres")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autenticação bem-sucedida",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Erro interno inesperado")
     *         )
     *     )
     * )
     */
    public function autenticarComEmailESenha(AuthUsuarioRequest $request): JsonResponse
    {
        try {
            $response = $this->service->autenticarComEmailESenha(
                ...$request->validated()
            );
        } catch (Throwable $error) {
            $response = [
                'error'   => true,
                'message' => $error->getMessage()
            ];

            $code = HttpResponse::HTTP_INTERNAL_SERVER_ERROR;

            if (array_key_exists($error->getCode(), HttpResponse::$statusTexts)) {
                $code = $error->getCode();
            }

            return Response::json($response, $code);
        }

        return Response::json($response);
    }

    /**
     * @OA\Get(
     *     path="/api/usuario/auth/identidade",
     *     tags={"Autenticação"},
     *     summary="Retorna os dados do usuário autenticado",
     *     description="Esse endpoint retorna os dados do usuário logado baseado no token JWT enviado no header.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", format="uuid", example="251ab069-02bd-474d-a21b-f8f214ab797c"),
     *             @OA\Property(property="nome", type="string", example="Atendente"),
     *             @OA\Property(property="email", type="string", example="uaragao@gmail.com"),
     *             @OA\Property(property="email_verificado_em", type="string", nullable=true, example=null),
     *             @OA\Property(property="status", type="string", example="ativo"),
     *             @OA\Property(property="excluido", type="boolean", example=false),
     *             @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *             @OA\Property(property="data_cadastro", type="string", example="07/08/2025 18:03"),
     *             @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token ausente ou inválido"
     *     )
     * )
     */
    public function identidade(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/api/usuario/auth/logout",
     *     tags={"Autenticação"},
     *     summary="Logout do usuário",
     *     description="Realiza o logout do usuário invalidando o token JWT atual.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token ausente ou inválido"
     *     )
     * )
     */
    public function logout(): array
    {
        auth()->logout();

        return [
            'message' => 'Successfully logged out'
        ];
    }
}
