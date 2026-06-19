<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

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
    public function boot(Request $request): void
    {
        App::setLocale('pt_BR');

        // Se o ambiente não for local (ou seja, estiver no Railway), força o HTTPS
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        $request->setTrustedProxies(['0.0.0.0/0'], Request::HEADER_X_FORWARDED_AWS_ELB);
    }
}
