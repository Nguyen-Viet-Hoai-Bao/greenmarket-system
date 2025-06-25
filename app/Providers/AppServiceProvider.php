<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cloudinary\Configuration\Configuration;

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
        Configuration::instance([
            'cloud' => [
                'cloud_name' => 'dth3mz6s9',
                'api_key'    => '792675684592192',
                'api_secret' => '7jbP0PFmXzSP0LrE_FsvfjAuFIo',
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }
}
