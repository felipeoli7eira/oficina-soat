<?php

namespace App\Providers;

use App\Domain\Entity\Usuario\RepositorioInterface as UsuarioRepository;
use App\Infrastructure\Repositories\UsuarioEloquentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UsuarioRepository::class,
            UsuarioEloquentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
