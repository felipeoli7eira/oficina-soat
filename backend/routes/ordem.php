<?php

use App\Http\Middleware\JsonWebTokenMiddleware;
use App\Http\OrdemApi;
use Illuminate\Support\Facades\Route;

// Route::withoutMiddleware(JsonWebTokenMiddleware::class)->group(function () {
    Route::post('/ordem', [OrdemApi::class, 'create']);

    Route::get('/ordem', [OrdemApi::class, 'read']);
    Route::get('/ordem/{uuid}', [OrdemApi::class, 'readOne']);

    Route::put('/ordem/{uuid}', [OrdemApi::class, 'update']);
    Route::delete('/ordem/{uuid}', [OrdemApi::class, 'delete']);
// });
