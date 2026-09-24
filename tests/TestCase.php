<?php

namespace StudioDevs\CookieConsent\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use StudioDevs\CookieConsent\CookieConsentServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [CookieConsentServiceProvider::class];
    }
}
