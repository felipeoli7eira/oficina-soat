<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('health', fn() => response()->json(['healthy' => true]));

// require __DIR__ . '/../app/Modules/Example/Routes/api.php';

// * endpoints de cliente
require __DIR__ . '/../app/Modules/Cliente/Routes/api.php';

// * endpoints de veiculo
require __DIR__ . '/../app/Modules/Veiculo/Routes/api.php';

// * endpoints de peca_insumo
require __DIR__ . '/../app/Modules/PecaInsumo/Routes/api.php';
