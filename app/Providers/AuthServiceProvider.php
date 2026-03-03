<?php

namespace App\Providers;

use App\Models\Envio;
use App\Policies\EnvioPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Envio::class => EnvioPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}