{{-- Button opening the preferences modal, e.g. for the footer (a button, since it's an action, not navigation). Renders nothing when no banner is needed. --}}
@if (app(\StudioDevs\CookieConsent\CookieConsent::class)->isActive())
    <button type="button" data-cc="show-preferencesModal" {{ $attributes }}>{{ $slot->isEmpty() ? app(\StudioDevs\CookieConsent\CookieConsent::class)->trans('settings_link') : $slot }}</button>
@endif
