<?php

namespace TromsFylkestrafikk\Pto;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TromsFylkestrafikk\Pto\Console\VehicleImportCsv;
use TromsFylkestrafikk\Pto\Console\CompanyImportCsv;

class PtoServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->publishConfig();
        $this->registerMigrations();
        $this->registerConsoleCommands();
        $this->registerRoutes();
    }

    protected function publishConfig()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/pto.php' => config_path('pto.php'),
            ], ['pto', 'config', 'pto-config']);
        }
    }

    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function registerRoutes()
    {
        Route::group(
            config('pto.routes', [
                'prefix' => 'api/pto',
                'middleware' => ['api']
            ]),
            function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            }
        );
    }

    protected function registerConsoleCommands()
    {
        $this->commands([
            CompanyImportCsv::class,
            VehicleImportCsv::class,
        ]);
    }
}
