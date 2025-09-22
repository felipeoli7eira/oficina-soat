<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Dominio\Gateway\UsuarioGateway;
use App\Dominio\Usuario\Repositorio\Contrato as UsuarioRepositorioContrato;
use App\Infraestrutura\Repositorio\UsuarioRepositorioEloquent;
use App\InterfaceAdaptador\Gateway\UsuarioGatewayImpl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UsuarioRepositorioContrato::class,
            UsuarioRepositorioEloquent::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
