<?php

use App\Http\Middleware\DocumentoObrigatorioMiddleware;
use App\Http\Middleware\JsonWebTokenMiddleware;
use App\Http\OrdemApi;
use Illuminate\Support\Facades\Route;

Route::post('/ordem', [OrdemApi::class, 'create']);

Route::get('/ordem', [OrdemApi::class, 'read']);
Route::get('/ordem/{uuid}', [OrdemApi::class, 'readOne']);

Route::put('/ordem/{uuid}', [OrdemApi::class, 'update']);
Route::put('/ordem/{uuid}/status', [OrdemApi::class, 'updateStatus']);

Route::post('/ordem/servico', [OrdemApi::class, 'addService']);
Route::delete('/ordem/servico', [OrdemApi::class, 'removeService']);

Route::post('/ordem/material', [OrdemApi::class, 'addMaterial']);
Route::delete('/ordem/material', [OrdemApi::class, 'removeMaterial']);

Route::middleware(DocumentoObrigatorioMiddleware::class)->group(function () {
    // endpoints somente para aprovacao/reprovacao de uma ordem
    Route::match(['get', 'put'], '/ordem/{uuid}/aprovacao', [OrdemApi::class, 'aprovacao'])->withoutMiddleware(JsonWebTokenMiddleware::class);
    Route::match(['get', 'put'], '/ordem/{uuid}/reprovacao', [OrdemApi::class, 'reprovacao'])->withoutMiddleware(JsonWebTokenMiddleware::class);
});
