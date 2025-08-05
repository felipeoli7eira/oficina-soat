<?php

declare(strict_types=1);

namespace App\Modules\OS\Controller;

use App\Http\Controllers\Controller as BaseController;

use App\Modules\OS\Requests\AtualizacaoRequest;
use App\Modules\OS\Requests\CadastroRequest;
use App\Modules\OS\Requests\ObterUmPorUuidRequest;

use App\Modules\OS\Service\Service as OSService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;
use Throwable;

class Controller extends BaseController
{
    public function __construct(private readonly OSService $service) {}

    /**
     * @OA\Get(
     *     path="/api/os",
     *     tags={"OS"},
     *     summary="Faz a listagem das ordens de serviço cadastradas no sistema",
     *     description="Retorna uma lista paginada de ordens de serviço cadastradas no sistema.",
     *     @OA\Response(
     *         response=200,
     *         description="Para quando a requisição for bem-sucedida. Pode ou não retornar ordens de serviço.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="uuid", type="string", format="uuid", example="2e895ab8-1183-4a03-94fd-005d68a7ebb2"),
     *                     @OA\Property(property="data_abertura", type="string", example="05/08/2025 18:28"),
     *                     @OA\Property(property="data_finalizacao", type="string", nullable=true, example=null),
     *                     @OA\Property(property="prazo_validade", type="integer", example=7),
     *                     @OA\Property(property="veiculo_id", type="integer", example=1),
     *                     @OA\Property(property="descricao", type="string", example="descricao qualquer..."),
     *                     @OA\Property(property="valor_desconto", type="number", format="float", example=100),
     *                     @OA\Property(property="valor_total", type="number", format="float", example=400),
     *                     @OA\Property(property="usuario_id_atendente", type="integer", example=1),
     *                     @OA\Property(property="usuario_id_mecanico", type="integer", example=3),
     *                     @OA\Property(property="excluido", type="boolean", example=false),
     *                     @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *                     @OA\Property(property="data_cadastro", type="string", example="2025-08-05 18:28:07"),
     *                     @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null)
     *                 )
     *             ),
     *             @OA\Property(property="first_page_url", type="string", example="http://localhost:8080/api/os?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=1),
     *             @OA\Property(property="last_page_url", type="string", example="http://localhost:8080/api/os?page=1"),
     *             @OA\Property(
     *                 property="links",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="label", type="string", example="&laquo; Anterior"),
     *                     @OA\Property(property="active", type="boolean", example=false)
     *                 )
     *             ),
     *             @OA\Property(property="next_page_url", type="string", nullable=true, example=null),
     *             @OA\Property(property="path", type="string", example="http://localhost:8080/api/os"),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *             @OA\Property(property="to", type="integer", example=7),
     *             @OA\Property(property="total", type="integer", example=7)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro inesperado no servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro interno do servidor.")
     *         )
     *     )
     * )
     */
    public function listagem()
    {
        try {
            $response = $this->service->listagem();
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
     *      path="/api/os",
     *      tags={"OS"},
     *      summary="Cadastra uma ordem de serviço",
     *      description="Cadastra uma ordem de serviço, dado os dados necessários.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"cliente_uuid", "veiculo_uuid", "descricao", "valor_desconto", "valor_total", "usuario_uuid_atendente", "usuario_uuid_mecanico", "prazo_validade"},
     *              @OA\Property(property="cliente_uuid", type="string", format="uuid", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *              @OA\Property(property="veiculo_uuid", type="string", format="uuid", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *              @OA\Property(property="descricao", type="string", example="descricao qualquer..."),
     *              @OA\Property(property="valor_desconto", type="number", format="float", example=100.0),
     *              @OA\Property(property="valor_total", type="number", format="float", example=400.0),
     *              @OA\Property(property="usuario_uuid_atendente", type="string", format="uuid", example="4a0a1310-cefd-4e02-935a-1d97017f7ec3"),
     *              @OA\Property(property="usuario_uuid_mecanico", type="string", format="uuid", example="742e08be-e5a0-4704-b9a1-cf4085d931fc"),
     *              @OA\Property(property="prazo_validade", type="integer", example=7)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Para quando a requisição for bem-sucedida. Retorna o cadastro da ordem de serviço.",
     *          @OA\JsonContent(
     *              @OA\Property(property="uuid", type="string", format="uuid", example="9b14c377-a173-443d-9f58-bd72eb3d2f60"),
     *              @OA\Property(property="data_abertura", type="string", example="05/08/2025 18:34"),
     *              @OA\Property(property="data_finalizacao", type="string", nullable=true, example=null),
     *              @OA\Property(property="prazo_validade", type="integer", example=7),
     *              @OA\Property(property="veiculo_id", type="integer", example=1),
     *              @OA\Property(property="descricao", type="string", example="descricao qualquer..."),
     *              @OA\Property(property="valor_desconto", type="number", format="float", example=100.0),
     *              @OA\Property(property="valor_total", type="number", format="float", example=400.0),
     *              @OA\Property(property="usuario_id_atendente", type="integer", example=1),
     *              @OA\Property(property="usuario_id_mecanico", type="integer", example=3),
     *              @OA\Property(property="excluido", type="boolean", example=false),
     *              @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *              @OA\Property(property="data_cadastro", type="string", format="date-time", example="2025-08-05 18:34:40"),
     *              @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null),
     *              @OA\Property(
     *                  property="cliente",
     *                  type="object",
     *                  @OA\Property(property="uuid", type="string", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *                  @OA\Property(property="nome", type="string", example="Thiago Ortega Velasques"),
     *                  @OA\Property(property="cpf", type="string", example="66371900552"),
     *                  @OA\Property(property="cnpj", type="string", nullable=true, example=null),
     *                  @OA\Property(property="email", type="string", example="meireles.elias@toledo.org"),
     *                  @OA\Property(property="telefone_movel", type="string", example="3533716392"),
     *                  @OA\Property(property="cep", type="string", example="43267955"),
     *                  @OA\Property(property="logradouro", type="string", example="R. João Aranda"),
     *                  @OA\Property(property="numero", type="string", example="258"),
     *                  @OA\Property(property="bairro", type="string", example="do Norte"),
     *                  @OA\Property(property="complemento", type="string", nullable=true, example=null),
     *                  @OA\Property(property="cidade", type="string", example="Pérola do Norte"),
     *                  @OA\Property(property="uf", type="string", example="TO"),
     *                  @OA\Property(property="excluido", type="boolean", example=false),
     *                  @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *                  @OA\Property(property="data_cadastro", type="string", example="28/03/2025 18:06"),
     *                  @OA\Property(property="data_atualizacao", type="string", example="20/07/2025 20:14")
     *              ),
     *              @OA\Property(
     *                  property="veiculo",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="uuid", type="string", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *                  @OA\Property(property="placa", type="string", example="BEY-6629"),
     *                  @OA\Property(property="modelo", type="string", example="Onix"),
     *                  @OA\Property(property="marca", type="string", example="Chevrolet"),
     *                  @OA\Property(property="ano_fabricacao", type="integer", example=2024),
     *                  @OA\Property(property="cor", type="string", example="Cinza"),
     *                  @OA\Property(property="chassi", type="string", example="3FT36VMMD1N3YLBN5"),
     *                  @OA\Property(property="excluido", type="boolean", example=false),
     *                  @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *                  @OA\Property(property="data_cadastro", type="string", format="date-time", example="2025-04-29 04:06:08"),
     *                  @OA\Property(property="data_atualizacao", type="string", format="date-time", example="2025-04-15 23:08:28")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Erro de validação no payload enviado.",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Dados enviados incorretamente"),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="string", example="O campo cliente_uuid é obrigatório.")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Erro interno inesperado no servidor.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Erro interno do servidor.")
     *          )
     *      )
     * )
     */
    public function cadastro(CadastroRequest $request)
    {
        try {
            $response = $this->service->cadastro($request->toDto());
        } catch (DomainException $error) {
            $response = [
                'error'   => true,
                'message' => $error->getMessage()
            ];

            return Response::json($response, $error->getCode());
        } catch (Throwable $error) {
            $response = [
                'error'   => true,
                'message' => $error->getMessage()
            ];

            return Response::json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response, HttpResponse::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/os/{uuid}",
     *     summary="Obtém os dados de uma ordem de serviço pelo uuid fornecido",
     *     description="Retorna as informações completas de uma ordem de serviço com base no UUID fornecido.",
     *     tags={"OS"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID da ordem de serviço que deseja consultar",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ordem de serviço encontrada com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="uuid", type="string", example="ce372925-6c2b-45ab-921e-85265d552324"),
     *             @OA\Property(property="data_abertura", type="string", example="05/08/2025 18:46"),
     *             @OA\Property(property="data_finalizacao", type="string", nullable=true, example=null),
     *             @OA\Property(property="prazo_validade", type="integer", example=7),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="descricao", type="string", example="descricao qualquer..."),
     *             @OA\Property(property="valor_desconto", type="number", format="float", example=100),
     *             @OA\Property(property="valor_total", type="number", format="float", example=400),
     *             @OA\Property(property="usuario_id_atendente", type="integer", example=1),
     *             @OA\Property(property="usuario_id_mecanico", type="integer", example=3),
     *             @OA\Property(property="excluido", type="boolean", example=false),
     *             @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *             @OA\Property(property="data_cadastro", type="string", example="2025-08-05 18:46:40"),
     *             @OA\Property(property="data_atualizacao", type="string", nullable=true, example=null),
     *             @OA\Property(
     *                 property="cliente",
     *                 type="object",
     *                 @OA\Property(property="uuid", type="string", example="3d17aa66-d72f-4861-9ef6-5bcbc5d2b5ba"),
     *                 @OA\Property(property="nome", type="string", example="Thiago Ortega Velasques"),
     *                 @OA\Property(property="cpf", type="string", example="66371900552"),
     *                 @OA\Property(property="cnpj", type="string", nullable=true, example=null),
     *                 @OA\Property(property="email", type="string", example="meireles.elias@toledo.org"),
     *                 @OA\Property(property="telefone_movel", type="string", example="3533716392"),
     *                 @OA\Property(property="cep", type="string", example="43267955"),
     *                 @OA\Property(property="logradouro", type="string", example="R. João Aranda"),
     *                 @OA\Property(property="numero", type="string", example="258"),
     *                 @OA\Property(property="bairro", type="string", example="do Norte"),
     *                 @OA\Property(property="complemento", type="string", nullable=true, example=null),
     *                 @OA\Property(property="cidade", type="string", example="Pérola do Norte"),
     *                 @OA\Property(property="uf", type="string", example="TO"),
     *                 @OA\Property(property="excluido", type="boolean", example=false),
     *                 @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *                 @OA\Property(property="data_cadastro", type="string", example="28/03/2025 18:06"),
     *                 @OA\Property(property="data_atualizacao", type="string", example="20/07/2025 20:14")
     *             ),
     *             @OA\Property(
     *                 property="veiculo",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uuid", type="string", example="17dcbf4f-5c3b-4b69-8d6b-76b592cb47d5"),
     *                 @OA\Property(property="placa", type="string", example="BEY-6629"),
     *                 @OA\Property(property="modelo", type="string", example="Onix"),
     *                 @OA\Property(property="marca", type="string", example="Chevrolet"),
     *                 @OA\Property(property="ano_fabricacao", type="integer", example=2024),
     *                 @OA\Property(property="cor", type="string", example="Cinza"),
     *                 @OA\Property(property="chassi", type="string", example="3FT36VMMD1N3YLBN5"),
     *                 @OA\Property(property="excluido", type="boolean", example=false),
     *                 @OA\Property(property="data_exclusao", type="string", nullable=true, example=null),
     *                 @OA\Property(property="data_cadastro", type="string", example="2025-04-29 04:06:08"),
     *                 @OA\Property(property="data_atualizacao", type="string", example="2025-04-15 23:08:28")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ordem de serviço não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erros de validação"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(type="string", example="O campo uuid selecionado é inválido.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro não mapeado na aplicação ou no endpoint",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro interno do servidor.")
     *         )
     *     )
     * )
     */
    public function obterUmPorUuid(ObterUmPorUuidRequest $request)
    {
        try {
            $response = $this->service->obterUmPorUuid($request->uuid);
        } catch (ModelNotFoundException $th) {
            return Response::json([
                'error'   => true,
                'message' => 'Nenhum registro correspondente ao informado'
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
     *     path="/api/os/{uuid}",
     *     summary="Remove uma ordem de serviço",
     *     description="Remove uma ordem de serviço com base no UUID informado.",
     *     tags={"OS"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID da ordem de serviço a ser removida",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Ordem de serviço removida com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ordem de serviço não encontrado(a)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erros de validação"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(type="string", example="O campo uuid selecionado é inválido.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno não tratado"
     *     )
     * )
     */
    public function remocao(ObterUmPorUuidRequest $request)
    {
        try {
            $response = $this->service->remocao($request->uuid);
        } catch (ModelNotFoundException $th) {
            return Response::json([
                'error'   => true,
                'message' => 'Nenhum registro correspondente ao informado'
            ], HttpResponse::HTTP_NOT_FOUND);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response, HttpResponse::HTTP_NO_CONTENT);
    }

    public function atualizacao(AtualizacaoRequest $request)
    {
        try {
            $response = $this->service->atualizacao($request->uuid(), $request->toDto());
        } catch (ModelNotFoundException $th) {
            return Response::json([
                'error'   => true,
                'message' => 'Nenhum registro correspondente ao informado'
            ], HttpResponse::HTTP_NOT_FOUND);
        } catch (Throwable $th) {
            return Response::json([
                'error'   => true,
                'message' => $th->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($response);
    }

    // public function finaluzar(ObterUmPorUuidRequest $request)
    // {
    //     try {
    //         $response = $this->service->finalizar($request->uuid());
    //     } catch (ModelNotFoundException $th) {
    //         return Response::json([
    //             'error'   => true,
    //             'message' => 'Nenhum registro correspondente ao informado'
    //         ], HttpResponse::HTTP_NOT_FOUND);
    //     } catch (Throwable $th) {
    //         return Response::json([
    //             'error'   => true,
    //             'message' => $th->getMessage()
    //         ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     return Response::json($response);
    // }
}
