<?php

declare(strict_types=1);

use App\Modules\Auth\Controllers\AuthUsuarioController;
use App\Modules\Usuario\Controller\Controller as UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/usuario', [UsuarioController::class, 'listagem']);
Route::get('/usuario/{uuid}', [UsuarioController::class, 'obterUmPorUuid']);

Route::post('/usuario/auth/autenticar', [AuthUsuarioController::class, 'autenticarComEmailESenha'])->withoutMiddleware('auth:api');
Route::get('/usuario/auth/identidade', [AuthUsuarioController::class, 'identidade']);
Route::get('/usuario/auth/logout', [AuthUsuarioController::class, 'logout']);

Route::post('/usuario', [UsuarioController::class, 'cadastro'])->withoutMiddleware('auth:api');

Route::put('/usuario/{uuid}', [UsuarioController::class, 'atualizacao']);
Route::delete('/usuario/{uuid}', [UsuarioController::class, 'remocao']);
