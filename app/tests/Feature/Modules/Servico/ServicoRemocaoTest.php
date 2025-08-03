<?php

namespace Tests\Feature\Modules\Servico;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response = $this->deleteJson('/api/servico/' . $servico->uuid);
        $response->assertNoContent();

        $response = $this->getJson('/api/servico/' . $servico->uuid);
        $response->assertNotFound();
    }

    public function test_exclusao_logica_do_servico_por_uuid_que_nao_existe(): void
    {
        $uuid = fake()->uuid();
        $response = $this->deleteJson('/api/servico/' . $uuid);
        $response->assertNotFound();
    }

    public function test_exclusao_logica_do_servico_por_uuid_com_formato_invalido(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380-invalido';
        $response = $this->deleteJson('/api/servico/' . $uuid);
        $response->assertUnprocessable();
    }  
}
