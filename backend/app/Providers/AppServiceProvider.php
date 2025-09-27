<?php

namespace App\Providers;

use App\Domain\Usuario\RepositorioInterface as UsuarioRpository;
use App\Infrastructure\Repositories\UsuarioEloquentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UsuarioRpository::class,
            UsuarioEloquentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
