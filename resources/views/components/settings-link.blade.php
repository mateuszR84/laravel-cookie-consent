{{-- Przycisk otwierający okno ustawień, np. do stopki (button, bo to akcja, nie nawigacja). Nic nie renderuje, gdy baner nie jest potrzebny. --}}
@if (app(\StudioDevs\CookieConsent\CookieConsent::class)->isActive())
    <button type="button" data-cc="show-preferencesModal" {{ $attributes }}>{{ $slot->isEmpty() ? app(\StudioDevs\CookieConsent\CookieConsent::class)->trans('settings_link') : $slot }}</button>
@endif
