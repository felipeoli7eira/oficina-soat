<?php

namespace Tests\Feature\Modules\Servico;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Tests\TestCase;

class ServicoListagemTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('servicos');
    }

    public function test_listar_todos_servicos(): void
    {
        $servicos = \App\Modules\Servico\Model\Servico::factory(3)->create();
        $response = $this->getJson('/api/servico');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        $response->assertJson(function (AssertableJson $json) use($servicos){
            $json->has('data.0.uuid')
                 ->has('data.0.descricao')
                 ->has('data.0.valor')
                 ->has('data.0.status')
                 ->etc();

            $servico = $servicos->first();
            $json->whereAll([
                'data.0.descricao' => $servico->descricao,
                'data.0.valor'     => (int) $servico->valor,
                'data.0.status'    => $servico->status,
            ]);
        });
    }

    public function test_listar_todos_servicos_com_erro_500_interno(): void
    {
        $mock = \Mockery::mock(\App\Modules\Servico\Service\Service::class);
        $mock->shouldReceive('listagem')
             ->andThrow(new \Exception('Erro simulado'));

        $this->app->instance(\App\Modules\Servico\Service\Service::class, $mock);

        $response = $this->getJson('/api/servico');

        $response->assertStatus(HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJson([
            'error' => true,
            'message' => 'Erro simulado'
        ]);
    }

    public function test_listar_servico_por_uuid(): void
    {
        $servico = \App\Modules\Servico\Model\Servico::factory(1)->createOne()->fresh();
        $servico = \App\Modules\Servico\Model\Servico::where('id', $servico->id)->first();
        $response = $this->getJson('/api/servico/' . $servico->uuid);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use($servico){
            $json->has('descricao')
                 ->has('valor')
                 ->has('status')
                 ->etc();

            $json->whereAll([
                'descricao' => $servico->descricao,
                'valor'     => (int) $servico->valor,
                'status'    => $servico->status,
            ]);
        });
    }

    public function test_listar_servico_por_uuid_com_erro_500_interno(): void
    {
        $servico = \App\Modules\Servico\Model\Servico::factory()->createOne()->fresh();
        $servico = \App\Modules\Servico\Model\Servico::where('id', $servico->id)->first();

        $mock = \Mockery::mock(\App\Modules\Servico\Service\Service::class);
        $mock->shouldReceive('obterUmPorUuid')
            ->with($servico->uuid)
            ->andThrow(new \Exception('Erro inesperado'));

        $this->app->instance(\App\Modules\Servico\Service\Service::class, $mock);

        $response = $this->getJson("/api/servico/{$servico->uuid}");

        $response->assertStatus(HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJson([
            'error' => true,
            'message' => 'Erro inesperado',
        ]);
    }

    public function test_listar_servico_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $response = $this->getJson('/api/servico/' . $uuid);
        $response->assertNotFound();
    }

    public function test_listar_servico_por_uuid_com_formato_invalido(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380-invalido';
        $response = $this->getJson('/api/servico/' . $uuid);
        $response->assertUnprocessable();
    }
}
