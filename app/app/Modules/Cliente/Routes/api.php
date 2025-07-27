<?php

declare(strict_types=1);

use App\Modules\Cliente\Controller\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/cliente', [ClienteController::class, 'listagem']);
Route::get('/cliente/{uuid}', [ClienteController::class, 'obterUmPorUuid']);
Route::post('/cliente', [ClienteController::class, 'cadastro']);
Route::put('/cliente/{uuid}', [ClienteController::class, 'atualizacao']);
Route::delete('/cliente/{uuid}', [ClienteController::class, 'remocao']);
