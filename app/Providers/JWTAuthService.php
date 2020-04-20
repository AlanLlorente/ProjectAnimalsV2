<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JWTAuthService extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require app_path().'\Helpers/JWTAuth.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
