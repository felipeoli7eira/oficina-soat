<?php

use App\Http\UsuarioApi;
use Illuminate\Support\Facades\Route;

Route::post('/usuario', [UsuarioApi::class, 'create']);
Route::get('/usuario', [UsuarioApi::class, 'read']);
Route::put('/usuario/{uuid}', [UsuarioApi::class, 'update']);
Route::delete('/usuario/{uuid}', [UsuarioApi::class, 'delete']);
