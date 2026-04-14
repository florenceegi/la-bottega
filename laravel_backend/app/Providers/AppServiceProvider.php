<?php

namespace App\Providers;

use App\Models\CorePersonalAccessToken;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Sanctum tokens live in core.personal_access_tokens (EGI shared)
        Sanctum::usePersonalAccessTokenModel(CorePersonalAccessToken::class);
    }
}
