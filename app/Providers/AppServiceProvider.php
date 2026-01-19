<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Fix for Yajra\Pdo\Oci8 compatibility with PHP 8+
        if (!defined('OCI_DEFAULT')) {
            define('OCI_DEFAULT', 0); // 0 is equivalent to OCI_NO_AUTO_COMMIT
        }
    }
}
