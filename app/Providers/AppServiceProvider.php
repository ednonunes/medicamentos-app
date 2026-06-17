<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        App::setLocale('pt_BR');
        // Força o uso de HTTPS se o ambiente não for local (produção/Railway)
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
