<?php

declare(strict_types=1);

use App\Modules\OrdemDeServicoItem\Controller\Controller as OSController;
use Illuminate\Support\Facades\Route;

Route::get('/os-item', [OSController::class, 'listagem']);
Route::get('/os-item/{uuid}', [OSController::class, 'obterUmPorUuid']);
Route::post('/os-item', [OSController::class, 'cadastro']);
Route::put('/os-item/{uuid}', [OSController::class, 'atualizacao']);
Route::delete('/os-item/{uuid}', [OSController::class, 'remocao']);
