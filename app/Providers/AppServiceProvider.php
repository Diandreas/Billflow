<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
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
        Schema::defaultStringLength(191);
        // Add this method to handle locale setting
        $this->handleLocale();

        // Add the SetLocale middleware to the web group
        Route::pushMiddlewareToGroup('web', \App\Http\Middleware\SetLocale::class);
    }
    protected function handleLocale(): void
    {
        if (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        }
    }
}
