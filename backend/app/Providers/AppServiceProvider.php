<?php

namespace App\Providers;

use App\Domain\Entity\Usuario\RepositorioInterface as UsuarioRepository;
use App\Infrastructure\Service\JsonWebToken;
use App\Infrastructure\Repositories\UsuarioEloquentRepository;
use App\Infrastructure\Service\LaravelAuthService;
use App\Signature\AuthServiceInterface;
use App\Signature\TokenServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UsuarioRepository::class,
            UsuarioEloquentRepository::class
        );

        $this->app->bind(
            TokenServiceInterface::class,
            JsonWebToken::class
        );

        $this->app->bind(
            AuthServiceInterface::class,
            LaravelAuthService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
