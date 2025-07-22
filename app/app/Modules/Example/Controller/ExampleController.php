<?php

declare(strict_types=1);

namespace App\Modules\Example\Controller;

use Throwable;
use App\Http\Controllers\Controller;
use App\Modules\Example\Dto\ExampleReadDto;
use Illuminate\Support\Facades\Response;
use App\Modules\Example\Requests\ExampleRequest;
use App\Modules\Example\Service\ExampleService;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * @OA\Info(
 *     title="Minha API",
 *     version="1.0.0",
 *     description="Documentação da minha API com L5-Swagger"
 * )
 */
class ExampleController extends Controller
{
    public function __construct(private readonly ExampleService $exampleService)
    {

    }

    /**
     * @OA\Get(
     *     path="/api/example",
     *     tags={"Example"},
     *     summary="Exemplo de endpoint",
     *     description="Retorna um exemplo de mensagem.",
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Hello World")
     *         )
     *     )
     * )
     */
    public function index(ExampleRequest $request)
    {
        try {
            $dto = new ExampleReadDto(
                message: $request->input('message', '...')
            );

            $response = $this->exampleService->example($dto);
        } catch (Throwable $th) {
            return Response::json([
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json([
            'response' => $response
        ]);
    }
}
