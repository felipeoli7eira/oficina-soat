<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Modules\Example\Controller\ExampleController;

Route::get('/example', [ExampleController::class, 'index']);
