<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Insumo;
use App\Observers\InsumoObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Insumo::observe(InsumoObserver::class);
    }
}