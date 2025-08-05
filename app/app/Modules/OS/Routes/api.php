<?php

declare(strict_types=1);

use App\Modules\OS\Controller\Controller as OSController;
use Illuminate\Support\Facades\Route;

Route::get('/os', [OSController::class, 'listagem']);
Route::get('/os/{uuid}', [OSController::class, 'obterUmPorUuid']);
Route::post('/os', [OSController::class, 'cadastro']);
Route::put('/os/{uuid}', [OSController::class, 'atualizacao']);
Route::put('/os/{uuid}/finalizar', [OSController::class, 'finaluzar']);
Route::delete('/os/{uuid}', [OSController::class, 'remocao']);
