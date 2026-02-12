<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Insumo;
use App\Observers\InsumoObserver;

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
        Insumo::observe(InsumoObserver::class);
    }
}
