<?php

namespace TromsFylkestrafikk\Pto;

use Illuminate\Support\ServiceProvider;

class PtoServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->setupMigrations();
    }

    protected function setupMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
