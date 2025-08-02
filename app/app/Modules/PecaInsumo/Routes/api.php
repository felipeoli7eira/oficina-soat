<?php

declare(strict_types=1);

use App\Modules\PecaInsumo\Controller\PecaInsumoController;
use Illuminate\Support\Facades\Route;

Route::get('/peca_insumo', [PecaInsumoController::class, 'listagem']);
Route::get('/peca_insumo/{id}', [PecaInsumoController::class, 'obterUmPorId']);
Route::post('/peca_insumo', [PecaInsumoController::class, 'cadastro']);
Route::put('/peca_insumo/{id}', [PecaInsumoController::class, 'atualizacao']);
Route::delete('/peca_insumo/{id}', [PecaInsumoController::class, 'remocao']);
