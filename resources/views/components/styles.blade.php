{{-- Goes into <head>. Renders nothing when no banner is needed. --}}
@php($cookieConsent = app(\StudioDevs\CookieConsent\CookieConsent::class))
@if ($cookieConsent->isActive())
    <link rel="stylesheet" href="{{ $cookieConsent->assetUrl('cookieconsent.css') }}">
    <link rel="stylesheet" href="{{ $cookieConsent->assetUrl('cookie-consent.css') }}">
@endif
