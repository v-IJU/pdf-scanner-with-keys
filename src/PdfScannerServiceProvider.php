<?php

namespace Dtech\PdfScanner;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PdfScannerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 1. Load the package views (use them as 'pdf-scanner::view-name')
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pdf-scanner');

        // 2. Register the Test Routes
        $this->registerRoutes();

        // 3. Allows the user to run: php artisan vendor:publish
        if ($this->app->runningInConsole()) {
            $this->publishes([
                // Publish Config
                __DIR__.'/../config/pdf-scanner.php' => config_path('pdf-scanner.php'),
                
                // Publish Views (Optional: if the user wants to customize the UI)
                __DIR__.'/../resources/views' => resource_path('views/vendor/pdf-scanner'),
            ], 'pdf-scanner-assets');
        }
    }

    public function register()
    {
        // Merge the default config
        $this->mergeConfigFrom(__DIR__.'/../config/pdf-scanner.php', 'pdf-scanner');
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes()
    {
        Route::group([
            'middleware' => ['web'], // Ensures CSRF and Sessions work
            'namespace'  => 'Dtech\PdfScanner',
        ], function () {
            Route::get('test-package', [PdfTestController::class, 'index'])->name('pdf-scanner.test');
            Route::post('test-package', [PdfTestController::class, 'scan'])->name('pdf-scanner.scan');
        });
    }
}