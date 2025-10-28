<?php

namespace EGanesha\CasAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use EGanesha\CasAuth\Http\Middleware\CasAuthMiddleware;

class CasServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        // Publish file konfigurasi
        $this->publishes([
            __DIR__ . '/../config/cas.php' => config_path('cas.php'),
        ], 'config');

        // Daftarkan middleware
        // Sekarang PHP tahu 'CasAuthMiddleware' itu apa
        $router->aliasMiddleware('cas.auth', CasAuthMiddleware::class);
    }

    public function register()
    {
        // Gabungkan config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/cas.php',
            'cas'
        );

        // Bind CasManager ke service container
        $this->app->singleton('cas.manager', function ($app) {
            return new CasManager($app['config']['cas']);
        });

        // (Opsional) Daftarkan Facade
        $this->app->alias('cas.manager', CasManager::class);
    }
}
