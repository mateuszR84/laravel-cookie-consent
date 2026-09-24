{{-- Link otwierający okno ustawień, np. do stopki. Nic nie renderuje, gdy baner nie jest potrzebny. --}}
@if (app(\StudioDevs\CookieConsent\CookieConsent::class)->isActive())
    <a href="#" data-cc="show-preferencesModal" {{ $attributes }}>{{ $slot->isEmpty() ? app(\StudioDevs\CookieConsent\CookieConsent::class)->trans('settings_link') : $slot }}</a>
@endif
