<?php

use App\Infrastructure\Web\ClienteWebController;
use Illuminate\Support\Facades\Route;

Route::post('/cliente', [ClienteWebController::class, 'create']);

Route::get('/cliente', [ClienteWebController::class, 'read']);
Route::get('/cliente/{uuid}', [ClienteWebController::class, 'readOneByUuid']);

Route::put('/cliente/{uuid}', [ClienteWebController::class, 'update']);
Route::delete('/cliente/{uuid}', [ClienteWebController::class, 'delete']);
