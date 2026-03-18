<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Presenter;

use App\Infrastructure\Presenter\HttpJsonPresenter;
use App\Signature\PresenterInterface;
use Tests\TestCase;

class HttpJsonPresenterTest extends TestCase
{
    public function testImplementsPresenterInterface()
    {
        $presenter = new HttpJsonPresenter();
        $this->assertInstanceOf(PresenterInterface::class, $presenter);
    }

    public function testToPresentRetornaJsonResponse()
    {
        $presenter = new HttpJsonPresenter();
        $dados = ['uuid' => 'uuid-123', 'nome' => 'João Silva'];

        $response = $presenter->toPresent($dados);

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSetStatusCodeAlteraStatusCode()
    {
        $presenter = new HttpJsonPresenter();

        $result = $presenter->setStatusCode(201);

        $this->assertInstanceOf(HttpJsonPresenter::class, $result);

        $response = $presenter->toPresent(['msg' => 'criado']);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testToPresentComArrayVazio()
    {
        $presenter = new HttpJsonPresenter();

        $response = $presenter->toPresent([]);

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSetStatusCodeRetornaInstanciaDePresenter()
    {
        $presenter = new HttpJsonPresenter();

        $result = $presenter->setStatusCode(404);

        $this->assertInstanceOf(HttpJsonPresenter::class, $result);
    }
}
