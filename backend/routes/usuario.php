<?php

use Illuminate\Support\Facades\Route;
use App\Infrastructure\Web\UsuarioWebController;
use App\Http\Middleware\JsonWebTokenMiddleware;

Route::withoutMiddleware(JsonWebTokenMiddleware::class)->group(function () {
    Route::get('/usuario', [UsuarioWebController::class, 'read']);
    Route::get('/usuario/{uuid}', [UsuarioWebController::class, 'readOneByUuid']);
    Route::post('/usuario', [UsuarioWebController::class, 'create']);

    Route::put('/usuario/{uuid}', [UsuarioWebController::class, 'update']);
    Route::delete('/usuario/{uuid}', [UsuarioWebController::class, 'delete']);
});
