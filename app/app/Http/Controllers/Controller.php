<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Oficina SOAT",
 *     version="1.0.0",
 *     description="Documentação da api do tech challenge FIAP 06.2025"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Coloque aqui o seu JWT de autorização. Você pode conseguir ele na collection de Autenticação, no endpoint /auth/autenticar.",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */

abstract class Controller
{
}
