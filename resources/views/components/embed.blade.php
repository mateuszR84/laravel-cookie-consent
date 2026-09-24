{{--
    Third-party iframe (map, video) inserted only after consent to its category.
    Every attribute except "category" and "src" goes onto the iframe. The slot replaces the default placeholder text.
    When the category needs no consent (inactive in the config, or the package disabled) - a plain iframe.
--}}
@props(['category' => 'media', 'src'])
@php($cookieConsent = app(\StudioDevs\CookieConsent\CookieConsent::class))
@if (! $cookieConsent->requiresConsent($category))
    <iframe src="{{ $src }}" {{ $attributes }}></iframe>
@else
    <div class="cc-embed"
         data-cc-embed="{{ $category }}"
         data-cc-embed-src="{{ $src }}"
         data-cc-embed-attrs="{{ json_encode($attributes->getAttributes()) }}">
        <div class="cc-embed__placeholder" data-cc-embed-placeholder>
            @if ($slot->isEmpty())
                <p class="cc-embed__description">{{ $cookieConsent->trans('embed.description', ['category' => $cookieConsent->categoryTitle($category)]) }}</p>
            @else
                {{ $slot }}
            @endif
            <button type="button" class="cc-embed__button" data-cc-embed-accept>{{ $cookieConsent->trans('embed.accept') }}</button>
            <button type="button" class="cc-embed__settings" data-cc="show-preferencesModal">{{ $cookieConsent->trans('settings_link') }}</button>
        </div>
    </div>
@endif
