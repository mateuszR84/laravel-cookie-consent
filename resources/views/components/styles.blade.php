{{-- Do <head>. Nic nie renderuje, gdy baner nie jest potrzebny. --}}
@php($cookieConsent = app(\StudioDevs\CookieConsent\CookieConsent::class))
@if ($cookieConsent->isActive())
    <link rel="stylesheet" href="{{ $cookieConsent->assetUrl('cookieconsent.css') }}">
    <link rel="stylesheet" href="{{ $cookieConsent->assetUrl('cookie-consent.css') }}">
@endif
