<?php

declare(strict_types=1);

namespace App\Modules\Veiculo\Controller;

use App\Http\Controllers\Controller;
use App\Modules\Veiculo\Dto\AtualizacaoDto;
use App\Modules\Veiculo\Dto\CadastroDto;
use App\Modules\Veiculo\Dto\ListagemDto;
use App\Modules\Veiculo\Service\Service as VeiculoService;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class VeiculoController extends Controller
{
    public function __construct(private readonly VeiculoService $service) {}

    /**
     * @OA\Get(
     *      path="/api/veiculo",
     *      tags={"Veiculo"},
     *      summary="Veiculo",
     *      description="Faz a listagem de veículos",
     *      @OA\Response(
     *          response=200,
     *          description="Para quando a requisição for bem sucedida. Pode ou não retornar veículos.",
     *       ),
     *     )
     */
    public function listagem()
    {
        try {
            $dto = new ListagemDto();
            $response = $this->service->listagem($dto);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }

    /**
     * @OA\Post(
     *      path="/api/veiculo",
     *      tags={"Veiculo"},
     *      summary="Cadastrar veículo",
     *      description="Cadastra um novo veículo",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"marca","modelo","ano","placa","chassi"},
     *              @OA\Property(property="marca", type="string", example="Toyota"),
     *              @OA\Property(property="modelo", type="string", example="Corolla"),
     *              @OA\Property(property="ano", type="integer", example=2020),
     *              @OA\Property(property="placa", type="string", example="ABC-1234"),
     *              @OA\Property(property="cor", type="string", example="Prata"),
     *              @OA\Property(property="chassi", type="string", example="1234567890ABCDEFG"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Veículo cadastrado com sucesso",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Dados de entrada inválidos",
     *      ),
     *     )
     */
    public function cadastro()
    {
        try {
            $dto = $request->toDto();
            $response = $this->service->cadastro($dto);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return Response::json($response, HttpResponse::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *      path="/api/veiculo/{uuid}",
     *      tags={"Veiculo"},
     *      summary="Obter veículo por UUID",
     *      description="Obtém um veículo específico pelo seu UUID",
     *      @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string", format="uuid")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Veículo encontrado com sucesso",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Veículo não encontrado",
     *      ),
     *     )
     */
    public function obterUmPorUuid(string $uuid)
    {
        try {
            $response = $this->service->obterUmPorUuid($request->uuid);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }

    /**
     * @OA\Delete(
     *      path="/api/veiculo/{uuid}",
     *      tags={"Veiculo"},
     *      summary="Remover veículo",
     *      description="Remove um veículo pelo seu UUID",
     *      @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string", format="uuid")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Veículo removido com sucesso",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Veículo não encontrado",
     *      ),
     *     )
     */
    public function remocao(string $uuid)
    {
        try {
            $response = $this->service->remocao($request->uuid);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response, HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Put(
     *      path="/api/veiculo/{uuid}",
     *      tags={"Veiculo"},
     *      summary="Atualizar veículo",
     *      description="Atualiza os dados de um veículo pelo seu UUID",
     *      @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string", format="uuid")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Veículo atualizado com sucesso",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Veículo não encontrado",
     *      ),
     *     )
     */
    public function atualizacao(string $uuid)
    {
        try {
            $response = $this->service->atualizacao($request->route('uuid'), $request->toDto());
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }
}
