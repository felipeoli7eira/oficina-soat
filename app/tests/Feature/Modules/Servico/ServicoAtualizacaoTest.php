<?php

namespace Tests\Feature\Modules\Servico;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class ServicoAtualizacaoTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('servicos');

        $fake = fake('pt_BR');

        $this->payload = [
            'descricao' => 'Pintura',
            'valor'     => 150.00,
            'status'    => 'ATIVO',
        ];
    }

    public function test_atualizar_servico_por_uuid(): void
    {
        $servico = \App\Modules\Servico\Model\Servico::factory()->createOne()->fresh();
        $servico = \App\Modules\Servico\Model\Servico::where('id', $servico->id)->first();

        $this->payload['descricao'] = 'Serviço atualizado';
        $this->payload['valor'] = 200;
        $this->payload['status'] = 'ATIVO';

        $response = $this->putJson('/api/servico/' . $servico->uuid, $this->payload);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json){
            $json->has('descricao')
                 ->has('valor')
                 ->has('status')
                 ->etc();

            $json->whereAll([
                'descricao' => $this->payload['descricao'],
                'valor'     => 200,
                'status'    => $this->payload['status'],
            ]);
        });
    }

    public function test_atualizar_servico_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $this->payload['descricao'] = 'Serviço com UUID que não existe';
        $this->payload['valor'] = 200.00;
        $this->payload['status'] = 'ATIVO';
        $response = $this->putJson('/api/servico/' . $uuid, $this->payload);

        $response->assertNotFound();
    }

    public function test_atualizar_servico_por_uuid_com_formato_invalido(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380-invalido';
        $this->payload['descricao'] = 'Serviço com UUID inválido';
        $this->payload['valor'] = 200.00;
        $this->payload['status'] = 'ATIVO';
        $response = $this->putJson('/api/servico/' . $uuid, $this->payload);

        $response->assertUnprocessable();
    }

    public function test_atualizar_servico_usando_mock_com_erro_500_interno(): void
    {
        $servico = \App\Modules\Servico\Model\Servico::factory()->createOne()->fresh();
        $servico = \App\Modules\Servico\Model\Servico::where('id', $servico->id)->first();

        $mock = \Mockery::mock(\App\Modules\Servico\Service\Service::class);
        $mock->shouldReceive('atualizacao')
            ->with($servico->uuid)
            ->andThrow(new \Exception('Erro inesperado'));

        $this->app->instance(\App\Modules\Servico\Service\Service::class, $mock);

        $this->payload['descricao'] = 'Serviço com erro 500';
        $this->payload['valor'] = 200.00;
        $this->payload['status'] = 'ATIVO';
        $response = $this->putJson('/api/servico/' . $servico->uuid, $this->payload);

        $response->assertStatus(HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

     public function test_atualizar_servico_usando_mock_com_erro_404_interno(): void
    {
        $uuid = fake()->uuid();

        $mock = \Mockery::mock(\App\Modules\Servico\Service\Service::class);
        $mock->shouldReceive('atualizacao')
            ->with($uuid)
            ->andThrow(new ModelNotFoundException('Registro não encontrado'));

        $this->app->instance(\App\Modules\Servico\Service\Service::class, $mock);

        $response = $this->putJson("/api/servico/{$uuid}", $this->payload);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }
}
