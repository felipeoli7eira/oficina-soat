<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // "usuario" repository binding
        // $this->app->bind(
        //     UsuarioRepository::class,
        //     \App\Infrastructure\Repositories\UsuarioArrayRepository::class
        // );

        // "servicos" repository binding
        // $this->app->bind(
        //     ServicoRepository::class,
        //     ServicoEloquentRepository::class
        // );

        // "material" repository binding
        // $this->app->bind(
        //     MaterialRepository::class,
        //     MaterialEloquentRepository::class
        // );

        // "cliente" repository binding
        // $this->app->bind(
        //     ClienteRepository::class,
        //     ClienteEloquentRepository::class
        // );

        // "veiculo" repository binding
        // $this->app->bind(
        //     VeiculoRepository::class,
        //     VeiculoEloquentRepository::class
        // );

        // "ordem" repository binding
        // $this->app->bind(
        //     OrdemRepository::class,
        //     OrdemEloquentRepository::class
        // );

        // "token" service binding
        // $this->app->bind(
        //     TokenServiceInterface::class,
        //     JsonWebToken::class
        // );

        // "auth" service binding
        // $this->app->bind(
        //     AuthServiceInterface::class,
        //     LaravelAuthService::class
        // );



        // ------------ FASE 3 ------------ //

        // Camada de infra

        $this->app->bind(
            \App\Infrastructure\Service\UuidGenerator\UuidGeneratorContract::class,
            \App\Infrastructure\Service\UuidGenerator\LaravelUuidFacadeGenerator::class
        );

        $this->app->bind(
            \App\Infrastructure\Service\JsonWebTokenHandler\JsonWebTokenHandlerContract::class,
            \App\Infrastructure\Service\JsonWebTokenHandler\FirebaseJWT::class
        );

        // Repositorios

        $this->app->bind(
            \App\Domain\Usuario\RepositoryContract::class,
            // \App\Infrastructure\Repositories\UsuarioFileRepository::class
            \App\Infrastructure\Repositories\UsuarioPostgresEloquentRepo::class
        );

        $this->app->bind(
            \App\Domain\Cliente\RepositoryContract::class,
            \App\Infrastructure\Repositories\ClientePostgresEloquentRepo::class
        );

        $this->app->bind(
            \App\Domain\Veiculo\RepositoryContract::class,
            \App\Infrastructure\Repositories\VeiculoPostgresEloquentRepo::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
