<?php

namespace StudioDevs\CookieConsent;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use StudioDevs\CookieConsent\Http\Controllers\AssetController;

class CookieConsentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cookie-consent.php', 'cookie-consent');

        $this->app->singleton(CookieConsent::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cookie-consent');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'cookie-consent');
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'cookie-consent');

        // Poza grupą "web": pliki statyczne nie potrzebują sesji ani nie powinny ustawiać cookies.
        Route::get('cookie-consent/assets/{file}', AssetController::class)
            ->whereIn('file', array_keys(CookieConsent::ASSETS))
            ->name('cookie-consent.asset');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cookie-consent.php' => config_path('cookie-consent.php'),
            ], 'cookie-consent-config');

            $this->publishes([
                __DIR__.'/../lang' => $this->app->langPath('vendor/cookie-consent'),
            ], 'cookie-consent-lang');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/cookie-consent'),
            ], 'cookie-consent-views');
        }
    }
}
