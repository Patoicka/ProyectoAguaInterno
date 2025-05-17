<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Incident\Domain\Repositories\IncidentRepositoryInterface;
use Src\Incident\Infrastructure\Persistence\IncidentRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IncidentRepositoryInterface::class, IncidentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
