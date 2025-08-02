<?php

declare(strict_types=1);

namespace App\Modules\Servico\Controller;

use App\Http\Controllers\Controller;
use App\Modules\Servico\Dto\ListagemDto;
use App\Modules\Servico\Requests\AtualizacaoRequest;
use App\Modules\Servico\Requests\CadastroRequest;
use App\Modules\Servico\Requests\ListagemRequest;
use App\Modules\Servico\Requests\ObterUmPorUuidRequest;

use App\Modules\Servico\Service\Service as ServicoService;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class ServicoController extends Controller
{
    public function __construct(private readonly ServicoService $service) {}

    /**
     * @OA\Get(
     *      path="/api/servico",
     *      tags={"servico"},
     *      summary="Faz a listagem dos serviços cadastrados",
     *      description="Faz a listagem de serviços",
     *      @OA\Response(
     *          response=200,
     *          description="Para quando a requisição for bem-sucedida. Pode ou não retornar serviços.",
     *       ),
     *     )
     */
    public function listagem(ListagemRequest $request)
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
     *      path="/api/servico",
     *      tags={"servico"},
     *      summary="Cadastra um serviço",
     *      description="Cadastra um serviço",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"descricao", "valor", "status"},
     *              @OA\Property(property="descricao", type="string", example="Troca de pastilha de freio"),
     *              @OA\Property(property="valor", type="number", format="float", example=150.00),
     *              @OA\Property(property="status", type="string", example="ATIVO"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Para quando a requisição for bem-sucedida. Retorna o cadastro do serviço.",
     *      )
     * )
     */
    public function cadastro(CadastroRequest $request)
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
     *     path="/api/servico/{uuid}",
     *     summary="Obtém os dados de um serviço",
     *     description="Retorna os dados completos de um serviço específico com base no UUID informado.",
     *     tags={"servico"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID do serviço que deseja consultar",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Serviço encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="uuid", type="string", format="uuid", example="b53b68c6-bc87-4553-b72a-8b2d6dbad7d6"),
     *             @OA\Property(property="descricao", type="string", example="Troca de pastilha de freio"),
     *             @OA\Property(property="valor", type="number", format="float", example=150.00),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Serviço não encontrado"
     *     )
     * )
     */
    public function obterUmPorUuid(ObterUmPorUuidRequest $request)
    {
        try {
            $response = $this->service->obterUmPorUuid($request->uuid);
            
        }catch (ModelNotFoundException $th){
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_NOT_FOUND);
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
     *     path="/api/servico/{uuid}",
     *     summary="Remove um servico",
     *     description="Remove um serviço com base no UUID informado.",
     *     tags={"servico"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID do serviço a ser removido",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Serviço removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erros de validação"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Serviço não encontrado"
     *     )
     * )
     */
    public function remocao(ObterUmPorUuidRequest $request)
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
     *     path="/api/servico/{uuid}",
     *     summary="Atualiza os dados de um serviço",
     *     description="Atualiza os dados de um serviço já existente, identificado pelo UUID.",
     *     tags={"servico"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID do serviço",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="descricao", type="string", example="Troca de pastilha de freio"),
     *             @OA\Property(property="valor", type="number", format="float", example=150.00),
     *             @OA\Property(property="status", type="string", example="ATIVO"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Serviço atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Serviço atualizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dados enviados incorretamente"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Serviço não encontrado"
     *     )
     * )
     */
    public function atualizacao(AtualizacaoRequest $request)
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
