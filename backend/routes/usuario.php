<?php

use App\Http\Middleware\JsonWebTokenMiddleware;
use App\Http\UsuarioApi;
use Illuminate\Support\Facades\Route;

Route::post('/usuario', [UsuarioApi::class, 'create']);

Route::get('/usuario', [UsuarioApi::class, 'read'])->middleware([JsonWebTokenMiddleware::class]);

Route::put('/usuario/{uuid}', [UsuarioApi::class, 'update']);
Route::delete('/usuario/{uuid}', [UsuarioApi::class, 'delete']);
