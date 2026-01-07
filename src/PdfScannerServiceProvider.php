<?php

namespace Dtech\PdfScanner;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Dtech\PdfScanner\Rules;

class PdfScannerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 1️⃣ Load package views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pdf-scanner');

        // 2️⃣ Register test/demo routes
        $this->registerRoutes();

        // 3️⃣ Register default extraction rules (🔥 IMPORTANT)
        $this->registerRules();

        // 4️⃣ Publish assets (config + views)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/pdf-scanner.php' => config_path('pdf-scanner.php'),
                __DIR__ . '/../resources/views' => resource_path('views/vendor/pdf-scanner'),
            ], 'pdf-scanner-assets');
        }
    }

    public function register()
    {
        // Merge package config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/pdf-scanner.php',
            'pdf-scanner'
        );
    }

    /**
     * Register default rules into the Rule Registry
     */
    protected function registerRules(): void
    {
        RuleRegistry::register(new Rules\PanRule());
        RuleRegistry::register(new Rules\TanRule());
        RuleRegistry::register(new Rules\DateRule());
       // RuleRegistry::register(new Rules\InvoiceRule());
        RuleRegistry::register(new Rules\NameRule());
        RuleRegistry::register(new Rules\AmountRule());
        RuleRegistry::register(new Rules\EmailRule());
        RuleRegistry::register(new Rules\InvoiceNoRule());
        RuleRegistry::register(new Rules\InvoiceDateRule());
        RuleRegistry::register(new Rules\AadhaarRule());
        RuleRegistry::register(new Rules\AccountRule());
        RuleRegistry::register(new Rules\GstinRule());
        RuleRegistry::register(new Rules\IfscRule());
        RuleRegistry::register(new Rules\PhoneRule());
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        Route::group([
            'middleware' => ['web'],
        ], function () {
            Route::get('test-package', [PdfTestController::class, 'index'])
                ->name('pdf-scanner.test');

            Route::post('test-package', [PdfTestController::class, 'scan'])
                ->name('pdf-scanner.scan');
        });
    }
}
