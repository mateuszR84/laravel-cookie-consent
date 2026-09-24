<?php

use Illuminate\Support\Facades\Blade;
use StudioDevs\CookieConsent\Tests\TestCase;

uses(TestCase::class)->in('Feature');

function blade(string $template): string
{
    return (string) Blade::render($template);
}
