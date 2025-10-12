<?php

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
