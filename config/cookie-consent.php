<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Włącznik
    |--------------------------------------------------------------------------
    |
    | Wyłączenie usuwa baner, skrypty i link ustawień. Osadzenia
    | (<x-cookie-consent::embed>) renderują się wtedy od razu jako iframe.
    |
    */

    'enabled' => env('COOKIE_CONSENT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Kategorie wymagające zgody
    |--------------------------------------------------------------------------
    |
    | Kategoria "necessary" (cookies niezbędne, np. sesja i CSRF) jest zawsze
    | włączona i nie trzeba jej tu wpisywać. Baner pokazuje się tylko wtedy,
    | gdy aktywna jest co najmniej jedna kategoria poniżej - strona bez
    | analityki i bez osadzeń z zewnątrz nie potrzebuje banera wcale.
    |
    | Nowa kategoria = nowy klucz tutaj + tłumaczenie
    | cookie-consent::messages.categories.<klucz>.title/description
    | (np. w lang/vendor/cookie-consent/pl/messages.php projektu).
    |
    | 'auto_clear' - cookies usuwane, gdy użytkownik wycofa zgodę. Wartość
    | zaczynająca się i kończąca na "/" jest traktowana jako wyrażenie regularne.
    |
    */

    'categories' => [

        'analytics' => [
            'active' => (bool) env('GOOGLE_ANALYTICS_ID'),
            'auto_clear' => ['/^_ga/', '_gid'],
        ],

        // Osadzenia z zewnętrznych serwisów: Google Maps, YouTube itp.
        'media' => [
            'active' => env('COOKIE_CONSENT_MEDIA', false),
            'auto_clear' => [],
        ],

        'marketing' => [
            'active' => env('COOKIE_CONSENT_MARKETING', false),
            'auto_clear' => [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Google Analytics (GA4)
    |--------------------------------------------------------------------------
    |
    | Po ustawieniu ID skrypt gtag.js jest wstawiany jako zablokowany
    | (type="text/plain", data-category="analytics") i uruchamia się dopiero po
    | zgodzie na kategorię "analytics".
    |
    */

    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),

    /*
    |--------------------------------------------------------------------------
    | Polityka prywatności
    |--------------------------------------------------------------------------
    |
    | Nazwa trasy albo URL. Link trafia do stopki banera. null = bez linku.
    | Strona wielojęzyczna: tablica per język, np.
    | ['pl' => 'privacy.show', 'en' => 'privacy.show.en'].
    |
    */

    'privacy_policy' => null,

    /*
    |--------------------------------------------------------------------------
    | Język
    |--------------------------------------------------------------------------
    |
    | null = app()->getLocale(). Paczka ma tłumaczenia pl i en; dla innego
    | języka bez tłumaczeń używany jest 'fallback_locale'.
    |
    */

    'locale' => env('COOKIE_CONSENT_LOCALE'),

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Rewizja zgody
    |--------------------------------------------------------------------------
    |
    | Podbij, gdy zmieniają się kategorie albo sposób przetwarzania danych -
    | wszyscy odwiedzający zobaczą baner ponownie.
    |
    */

    'revision' => 1,

    /*
    |--------------------------------------------------------------------------
    | Cookie przechowujące zgodę
    |--------------------------------------------------------------------------
    */

    'cookie' => [
        'name' => 'cc_cookie',
        'expires_after_days' => 182,
    ],

    /*
    |--------------------------------------------------------------------------
    | Wygląd
    |--------------------------------------------------------------------------
    |
    | Przekazywane wprost do guiOptions biblioteki:
    | https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions
    | Kolory zmienia się zmiennymi CSS (--cc-btn-primary-bg itd.) w CSS projektu.
    |
    */

    'gui_options' => [
        'consentModal' => [
            'layout' => 'box',
            'position' => 'bottom left',
            'equalWeightButtons' => true,
        ],
        'preferencesModal' => [
            'layout' => 'box',
            'equalWeightButtons' => true,
        ],
    ],

];
