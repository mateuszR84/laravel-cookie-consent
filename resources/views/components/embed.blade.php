{{--
    Iframe z zewnętrznego serwisu (mapa, film), wstawiany dopiero po zgodzie na kategorię.
    Wszystkie atrybuty poza "category" i "src" trafiają na iframe. Slot zastępuje domyślny opis w placeholderze.
    Gdy kategoria nie wymaga zgody (nieaktywna w configu albo paczka wyłączona) - zwykły iframe.
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
            <a href="#" class="cc-embed__settings" data-cc="show-preferencesModal">{{ $cookieConsent->trans('settings_link') }}</a>
        </div>
    </div>
@endif
