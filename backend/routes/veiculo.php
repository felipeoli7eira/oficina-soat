<?php

use App\Http\VeiculoApi;
use Illuminate\Support\Facades\Route;

Route::post('/veiculo', [VeiculoApi::class, 'create']);

Route::get('/veiculo', [VeiculoApi::class, 'read']);
Route::get('/veiculo/{uuid}', [VeiculoApi::class, 'readOne']);

Route::put('/veiculo/{uuid}', [VeiculoApi::class, 'update']);
Route::delete('/veiculo/{uuid}', [VeiculoApi::class, 'delete']);
