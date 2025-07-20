<?php

namespace App\Providers;

use App\Contracts\TranslationRepositoryInterface;
use App\Contracts\TranslationServiceInterface;
use App\Repositories\TranslationRepository;
use App\Services\TranslationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TranslationRepositoryInterface::class, TranslationRepository::class);
        $this->app->bind(TranslationServiceInterface::class, TranslationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
