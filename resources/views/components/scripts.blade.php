{{-- Goes right before </body>. Renders nothing when no banner is needed. --}}
@php($cookieConsent = app(\StudioDevs\CookieConsent\CookieConsent::class))
@if ($cookieConsent->isActive())
    <script type="application/json" id="cookie-consent-config">{!! json_encode($cookieConsent->jsConfig(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script src="{{ $cookieConsent->assetUrl('cookieconsent.umd.js') }}" defer></script>
    <script src="{{ $cookieConsent->assetUrl('cookie-consent.js') }}" defer></script>

    @if ($gaId = $cookieConsent->googleAnalyticsId())
        {{-- Blocked (text/plain) - the library runs them only after consent to "analytics". --}}
        <script type="text/plain" data-category="analytics" async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script type="text/plain" data-category="analytics">
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', @json($gaId));
        </script>
    @endif
@endif
