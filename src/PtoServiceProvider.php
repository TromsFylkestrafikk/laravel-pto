<?php

namespace TromsFylkestrafikk\Pto;

use Illuminate\Support\ServiceProvider;
use TromsFylkestrafikk\Pto\Console\VehicleImportCsv;

class PtoServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->setupMigrations();
        $this->setupConsoleCommands();
    }

    protected function setupMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function setupConsoleCommands()
    {
        $this->commands([VehicleImportCsv::class]);
    }
}
