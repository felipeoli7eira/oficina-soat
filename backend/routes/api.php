<?php

use App\Http\Authentication;
use App\Http\Middleware\JsonWebTokenMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('ping', fn() => response()->json([
    'err' => false,
    'msg' => 'pong'
]));

require_once __DIR__ . '/usuario.php';

Route::fallback(fn() => response()->json([
    'err' => true,
    'msg' => 'Recurso não encontrado',
]));


// auth

Route::post('auth/login', [Authentication::class, 'authenticate'])->withoutMiddleware(
    JsonWebTokenMiddleware::class
);
