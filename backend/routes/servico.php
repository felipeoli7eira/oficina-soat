<?php

// use App\Http\Middleware\JsonWebTokenMiddleware;

use App\Infrastructure\Web\ServicoWebController;
use Illuminate\Support\Facades\Route;

// Route::withoutMiddleware(JsonWebTokenMiddleware::class)->group(function () {
    Route::post('/servico', [ServicoWebController::class, 'create']);

    Route::get('/servico', [ServicoWebController::class, 'read']);
    Route::get('/servico/{uuid}', [ServicoWebController::class, 'readOne']);

    Route::put('/servico/{uuid}', [ServicoWebController::class, 'update']);
    Route::delete('/servico/{uuid}', [ServicoWebController::class, 'delete']);
// });
