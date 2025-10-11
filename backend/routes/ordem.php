<?php

use App\Http\OrdemApi;
use Illuminate\Support\Facades\Route;

Route::post('/ordem', [OrdemApi::class, 'create']);

Route::get('/ordem', [OrdemApi::class, 'read']);
Route::get('/ordem/{uuid}', [OrdemApi::class, 'readOne']);

Route::put('/ordem/{uuid}', [OrdemApi::class, 'update']);
Route::delete('/ordem/{uuid}', [OrdemApi::class, 'delete']);

Route::post('/ordem/servico', [OrdemApi::class, 'addService']);
Route::post('/ordem/material', [OrdemApi::class, 'addMaterial']);
