<?php

use App\Drivers\Http\UsuarioApi;
use Illuminate\Support\Facades\Route;

Route::post('/usuario', [UsuarioApi::class, 'criar']);
Route::get('/usuario', [UsuarioApi::class, 'listar']);
Route::delete('/usuario/{uuid}', [UsuarioApi::class, 'deletar']);
Route::put('/usuario/{uuid}', [UsuarioApi::class, 'atualizar']);
