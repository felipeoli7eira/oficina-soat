<?php

declare(strict_types=1);

use App\Modules\Veiculo\Controller\VeiculoController;
use Illuminate\Support\Facades\Route;

Route::get('/veiculo', [VeiculoController::class, 'listagem']);
Route::get('/veiculo/{uuid}', [VeiculoController::class, 'obterUmPorUuid']);
Route::post('/veiculo', [VeiculoController::class, 'cadastro']);
Route::put('/veiculo/{uuid}', [VeiculoController::class, 'atualizacao']);
Route::delete('/veiculo/{uuid}', [VeiculoController::class, 'remocao']);
