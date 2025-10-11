<?php

namespace App\Providers;

use App\Domain\Entity\Usuario\RepositorioInterface as UsuarioRepository;
use App\Infrastructure\Service\JsonWebToken;
use App\Infrastructure\Repositories\UsuarioEloquentRepository;
use App\Infrastructure\Service\LaravelAuthService;
use App\Signature\AuthServiceInterface;
use App\Signature\TokenServiceInterface;
use Illuminate\Support\ServiceProvider;

use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepository;
use App\Infrastructure\Repositories\ServicoEloquentRepository;

use App\Domain\Entity\Material\RepositorioInterface as MaterialRepository;
use App\Infrastructure\Repositories\MaterialEloquentRepository;

use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepository;
use App\Infrastructure\Repositories\ClienteEloquentRepository;

use App\Domain\Entity\Veiculo\RepositorioInterface as VeiculoRepository;
use App\Infrastructure\Repositories\VeiculoEloquentRepository;

use App\Domain\Entity\Ordem\RepositorioInterface as OrdemRepository;
use App\Infrastructure\Repositories\OrdemEloquentRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // "usuario" repository binding
        $this->app->bind(
            UsuarioRepository::class,
            UsuarioEloquentRepository::class
        );

        // "servicos" repository binding
        $this->app->bind(
            ServicoRepository::class,
            ServicoEloquentRepository::class
        );

        // "material" repository binding
        $this->app->bind(
            MaterialRepository::class,
            MaterialEloquentRepository::class
        );

        // "cliente" repository binding
        $this->app->bind(
            ClienteRepository::class,
            ClienteEloquentRepository::class
        );

        // "veiculo" repository binding
        $this->app->bind(
            VeiculoRepository::class,
            VeiculoEloquentRepository::class
        );

        // "ordem" repository binding
        $this->app->bind(
            OrdemRepository::class,
            OrdemEloquentRepository::class
        );

        // "token" service binding
        $this->app->bind(
            TokenServiceInterface::class,
            JsonWebToken::class
        );

        // "auth" service binding
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
