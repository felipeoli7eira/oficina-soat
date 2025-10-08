<?php

use App\Http\Middleware\JsonWebTokenMiddleware;
use App\Http\ServicoApi;
use Illuminate\Support\Facades\Route;

// Route::withoutMiddleware(JsonWebTokenMiddleware::class)->group(function () {
    Route::post('/servico', [ServicoApi::class, 'create']);

    Route::get('/servico', [ServicoApi::class, 'read']);
    Route::get('/servico/{uuid}', [ServicoApi::class, 'readOne']);

    Route::put('/servico/{uuid}', [ServicoApi::class, 'update']);
    Route::delete('/servico/{uuid}', [ServicoApi::class, 'delete']);
// });
