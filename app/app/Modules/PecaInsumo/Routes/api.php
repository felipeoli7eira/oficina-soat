<?php

declare(strict_types=1);

use App\Modules\PecaInsumo\Controller\PecaInsumoController;
use Illuminate\Support\Facades\Route;

Route::get('/peca-insumo', [PecaInsumoController::class, 'listagem']);
Route::get('/peca-insumo/{uuid}', [PecaInsumoController::class, 'obterUmPorUuid']);
Route::post('/peca-insumo', [PecaInsumoController::class, 'cadastro']);
Route::put('/peca-insumo/{uuid}', [PecaInsumoController::class, 'atualizacao']);
Route::delete('/peca-insumo/{uuid}', [PecaInsumoController::class, 'remocao']);
