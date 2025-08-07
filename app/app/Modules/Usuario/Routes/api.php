<?php

declare(strict_types=1);

use App\Modules\Usuario\Controller\Controller as UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/usuario', [UsuarioController::class, 'listagem']);
Route::get('/usuario/{uuid}', [UsuarioController::class, 'obterUmPorUuid']);

Route::post('/usuario', [UsuarioController::class, 'cadastro']);
Route::post('/usuario/login', [UsuarioController::class, 'login']);

Route::put('/usuario/{uuid}', [UsuarioController::class, 'atualizacao']);
Route::delete('/usuario/{uuid}', [UsuarioController::class, 'remocao']);
