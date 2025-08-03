<?php

declare(strict_types=1);

use App\Modules\Servico\Controller\ServicoController;
use Illuminate\Support\Facades\Route;

Route::get('/servico', [ServicoController::class, 'listagem']);
Route::get('/servico/{uuid}', [ServicoController::class, 'obterUmPorUuid']);
Route::post('/servico', [ServicoController::class, 'cadastro']);
Route::put('/servico/{uuid}', [ServicoController::class, 'atualizacao']);
Route::delete('/servico/{uuid}', [ServicoController::class, 'remocao']);
