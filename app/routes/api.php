<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('health', fn() => response()->json(['healthy' => true]));

Route::get('login', fn() => response()->json([
    'error'   => true,
    'message' => 'É necessário autenticação para acessar os dados',
]))->name('login');

Route::middleware('auth:api')->group(function () {
    // * endpoints de cliente
    require __DIR__ . '/../app/Modules/Cliente/Routes/api.php';

    // * endpoints de veiculo
    require __DIR__ . '/../app/Modules/Veiculo/Routes/api.php';

    // * endpoints de serviços
    require __DIR__ . '/../app/Modules/Servico/Routes/api.php';

    // * endpoints de usuário
    require __DIR__ . '/../app/Modules/Usuario/Routes/api.php';

    // * endpoints de ordem de servico (OS)
    require __DIR__ . '/../app/Modules/OrdemDeServico/Routes/api.php';

    // * endpoints de peças e insumos
    require __DIR__ . '/../app/Modules/PecaInsumo/Routes/api.php';
});
