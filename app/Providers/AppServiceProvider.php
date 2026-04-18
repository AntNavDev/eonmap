<?php

declare(strict_types=1);

namespace App\Providers;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Services\FossilOccurrenceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            FossilOccurrenceServiceInterface::class,
            FossilOccurrenceService::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
