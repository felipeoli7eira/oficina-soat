<?php

declare(strict_types=1);

namespace App\Modules\Cliente\Controller;

use App\Http\Controllers\Controller;
use App\Modules\Cliente\Dto\ListagemDto;
use App\Modules\Cliente\Requests\AtualizacaoRequest;
use App\Modules\Cliente\Requests\CadastroRequest;
use App\Modules\Cliente\Requests\ListagemRequest;
use App\Modules\Cliente\Requests\ObterUmPorUuidRequest;

use App\Modules\Cliente\Service\Service as ClienteService;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class ClienteController extends Controller
{
    public function __construct(private readonly ClienteService $service) {}

    /**
     * @OA\Get(
     *      path="/api/cliente",
     *      tags={"cliente"},
     *      summary="Faz a listagem dos clientes cadastrados",
     *      description="Faz a listagem de clientes",
     *      @OA\Response(
     *          response=200,
     *          description="Para quando a requisição for bem-sucedida. Pode ou não retornar clientes.",
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
     *      path="/api/cliente",
     *      tags={"cliente"},
     *      summary="Cadastra um cliente",
     *      description="Cadastra um cliente",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"nome", "email", "telefone_movel", "cep", "logradouro", "bairro", "cidade", "uf"},
     *              @OA\Property(property="nome", type="string", example="Felipe Oliveira"),
     *              @OA\Property(property="cpf", type="string", example="03364266239"),
     *              @OA\Property(property="cnpj", type="string", example="50162731000101"),
     *              @OA\Property(property="email", type="string", format="email", example="me.felipeoliveira@gmail.com"),
     *              @OA\Property(property="telefone_movel", type="string", example="(96) 98415-7994"),
     *              @OA\Property(property="cep", type="string", example="68909-811"),
     *              @OA\Property(property="logradouro", type="string", example="Viela Ambrósio Rodrigues de Medeiros"),
     *              @OA\Property(property="numero", type="string", example="18"),
     *              @OA\Property(property="bairro", type="string", example="Portal D'Oeste"),
     *              @OA\Property(property="complemento", type="string", example="Casa 2"),
     *              @OA\Property(property="cidade", type="string", example="Osasco"),
     *              @OA\Property(property="uf", type="string", example="AP", maxLength=2)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Para quando a requisição for bem-sucedida. Retorna o cadastro do cliente.",
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
     *     path="/api/cliente/{uuid}",
     *     summary="Obtém os dados de um cliente",
     *     description="Retorna os dados completos de um cliente específico com base no UUID informado.",
     *     tags={"cliente"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID do cliente que deseja consultar",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente encontrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="uuid", type="string", format="uuid", example="b53b68c6-bc87-4553-b72a-8b2d6dbad7d6"),
     *             @OA\Property(property="nome", type="string", example="Felipe Oliveira"),
     *             @OA\Property(property="cpf", type="string", example="03364266239"),
     *             @OA\Property(property="cnpj", type="string", example="50162731000101"),
     *             @OA\Property(property="email", type="string", example="me.felipeoliveira@gmail.com"),
     *             @OA\Property(property="telefone_movel", type="string", example="(96) 98415-7994"),
     *             @OA\Property(property="cep", type="string", example="68909-811"),
     *             @OA\Property(property="logradouro", type="string", example="Viela Ambrósio Rodrigues de Medeiros"),
     *             @OA\Property(property="numero", type="string", example="18"),
     *             @OA\Property(property="bairro", type="string", example="Portal D'Oeste"),
     *             @OA\Property(property="complemento", type="string", example="Casa 2"),
     *             @OA\Property(property="cidade", type="string", example="Osasco"),
     *             @OA\Property(property="uf", type="string", example="AP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     */
    public function obterUmPorUuid(ObterUmPorUuidRequest $request)
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
     *     path="/api/cliente/{uuid}",
     *     summary="Remove um cliente",
     *     description="Remove um cliente com base no UUID informado.",
     *     tags={"cliente"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID do cliente a ser removido",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Cliente removido com sucesso"
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
     *         description="Cliente não encontrado"
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
     *     path="/api/cliente/{uuid}",
     *     summary="Atualiza os dados de um cliente",
     *     description="Atualiza os dados de um cliente já existente, identificado pelo UUID.",
     *     tags={"cliente"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID do cliente",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nome", type="string", example="João da Silva"),
     *             @OA\Property(property="cpf", type="string", example="12345678901"),
     *             @OA\Property(property="cnpj", type="string", example="12345678000199"),
     *             @OA\Property(property="email", type="string", example="joao@email.com"),
     *             @OA\Property(property="telefone_movel", type="string", example="(11) 91234-5678"),
     *             @OA\Property(property="cep", type="string", example="12345-678"),
     *             @OA\Property(property="logradouro", type="string", example="Rua Exemplo"),
     *             @OA\Property(property="numero", type="string", example="123A"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="complemento", type="string", example="Apartamento 101"),
     *             @OA\Property(property="cidade", type="string", example="São Paulo"),
     *             @OA\Property(property="uf", type="string", example="SP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cliente atualizado com sucesso")
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
     *         description="Cliente não encontrado"
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
