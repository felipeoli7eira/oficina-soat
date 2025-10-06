<?php

use App\Http\Middleware\JsonWebTokenMiddleware;
use App\Http\UsuarioApi;
use Illuminate\Support\Facades\Route;

Route::post('/usuario', [UsuarioApi::class, 'create'])->withoutMiddleware(JsonWebTokenMiddleware::class);

Route::get('/usuario', [UsuarioApi::class, 'read'])->withoutMiddleware(JsonWebTokenMiddleware::class);//->middleware([JsonWebTokenMiddleware::class]);

Route::put('/usuario/{uuid}', [UsuarioApi::class, 'update'])->withoutMiddleware(JsonWebTokenMiddleware::class);
Route::delete('/usuario/{uuid}', [UsuarioApi::class, 'delete'])->withoutMiddleware(JsonWebTokenMiddleware::class);
