<?php

declare(strict_types=1);

use App\Modules\OrdemDeServicoServico\Controller\Controller as OSServicoController;
use Illuminate\Support\Facades\Route;

Route::get('/os-servico', [OSServicoController::class, 'listagem']);
Route::get('/os-servico/{uuid}', [OSServicoController::class, 'obterUmPorUuid']);
Route::post('/os-servico', [OSServicoController::class, 'cadastro']);
Route::put('/os-servico/{uuid}', [OSServicoController::class, 'atualizacao']);
Route::delete('/os-servico/{uuid}', [OSServicoController::class, 'remocao']);
