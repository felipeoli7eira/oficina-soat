<?php

use App\Infrastructure\Web\VeiculoWebController;
use Illuminate\Support\Facades\Route;

Route::post('/veiculo', [VeiculoWebController::class, 'create']);

Route::get('/veiculo', [VeiculoWebController::class, 'read']);
Route::get('/veiculo/{uuid}', [VeiculoWebController::class, 'readOneByUuid']);

Route::put('/veiculo/{uuid}', [VeiculoWebController::class, 'update']);
Route::delete('/veiculo/{uuid}', [VeiculoWebController::class, 'delete']);
