<?php

use App\Http\MaterialApi;
use Illuminate\Support\Facades\Route;

Route::post('/material', [MaterialApi::class, 'create']);

Route::get('/material', [MaterialApi::class, 'read']);
Route::get('/material/{uuid}', [MaterialApi::class, 'readOne']);

Route::put('/material/{uuid}', [MaterialApi::class, 'update']);
Route::delete('/material/{uuid}', [MaterialApi::class, 'delete']);
