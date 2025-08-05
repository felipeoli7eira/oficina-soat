<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register(): void
    {
        // O Laravel resolve automaticamente as dependências
    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
    }
}
