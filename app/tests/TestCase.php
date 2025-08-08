<?php

namespace Tests;

use App\Modules\Usuario\Model\Usuario;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    public function withAuth(?Usuario $usuario = null): self
    {
        $usuarioAutenticado = $usuario ?: Usuario::factory()->create();

        $token = JWTAuth::fromUser($usuarioAutenticado);

        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);
    }
}
