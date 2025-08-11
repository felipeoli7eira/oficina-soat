<?php

namespace Tests\Feature\Modules\Servico;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ServicoRemocaoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('servicos');
    }

    public function test_exclusao_logica_do_servico_por_uuid(): void
    {
        $servico = \App\Modules\Servico\Model\Servico::factory(1)->createOne()->fresh();
        $response = $this->withAuth()->deleteJson('/api/servico/' . $servico->uuid);
        $response->assertNoContent();

        $response = $this->getJson('/api/servico/' . $servico->uuid);
        $response->assertNotFound();
    }

    public function test_exclusao_logica_do_servico_via_api_por_uuid_que_nao_existe(): void
    {
        $uuid = fake()->uuid();
        $response = $this->withAuth()->deleteJson('/api/servico/' . $uuid);
        $response->assertNotFound();
    }

    public function test_exclusao_logica_do_servico_via_api_por_uuid_com_formato_invalido(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380-invalido';
        $response = $this->withAuth()->deleteJson('/api/servico/' . $uuid);
        $response->assertUnprocessable();
    }

    public function test_exclusao_logica_do_servico_com_mock_com_erro_500_interno(): void
    {
        $servico = \App\Modules\Servico\Model\Servico::factory()->createOne()->fresh();
        $servico = \App\Modules\Servico\Model\Servico::where('id', $servico->id)->first();

        $mock = \Mockery::mock(\App\Modules\Servico\Service\Service::class);
        $mock->shouldReceive('remocao')
            ->with($servico->uuid)
            ->andThrow(new \Exception('Erro inesperado'));

        $this->app->instance(\App\Modules\Servico\Service\Service::class, $mock);

        $response = $this->withAuth()->deleteJson("/api/servico/{$servico->uuid}");

        $response->assertStatus(HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function test_exclusao_logica_do_servico_com_mock_com_erro_404_interno(): void
    {
       $uuid = fake()->uuid();

        $mock = \Mockery::mock(\App\Modules\Servico\Service\Service::class);
        $mock->shouldReceive('remocao')
            ->with($uuid)
            ->andThrow(new ModelNotFoundException('Regisrtro não encontrado'));

        $this->app->instance(\App\Modules\Servico\Service\Service::class, $mock);

        $response = $this->withAuth()->deleteJson("/api/servico/{$uuid}");

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }
}
