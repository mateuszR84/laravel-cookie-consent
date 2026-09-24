<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master switch
    |--------------------------------------------------------------------------
    |
    | When disabled, the banner, scripts and settings button are not rendered
    | and embeds (<x-cookie-consent::embed>) render as plain iframes.
    |
    */

    'enabled' => env('COOKIE_CONSENT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Categories that require consent
    |--------------------------------------------------------------------------
    |
    | The "necessary" category (strictly necessary cookies such as the session
    | and CSRF token) is always on and doesn't need to be listed here. The
    | banner only shows up when at least one category below is active - a site
    | without analytics or third-party embeds doesn't need a banner at all.
    |
    | New category = a new key here + a translation for
    | cookie-consent::messages.categories.<key>.title/description
    | (e.g. in your app's lang/vendor/cookie-consent/en/messages.php).
    |
    | 'auto_clear' - cookies erased when the user withdraws consent. A value
    | that starts and ends with "/" is treated as a regular expression.
    |
    */

    'categories' => [

        'analytics' => [
            'active' => (bool) env('GOOGLE_ANALYTICS_ID'),
            'auto_clear' => ['/^_ga/', '_gid'],
        ],

        // Content embedded from third-party services: Google Maps, YouTube, etc.
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
    | When an ID is set, gtag.js is rendered as a blocked script
    | (type="text/plain", data-category="analytics") and only runs once the
    | user consents to the "analytics" category.
    |
    */

    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),

    /*
    |--------------------------------------------------------------------------
    | Privacy policy
    |--------------------------------------------------------------------------
    |
    | A route name or a URL, linked in the banner footer. null = no link.
    | Multilingual sites can pass an array keyed by locale, e.g.
    | ['en' => 'privacy', 'pl' => 'privacy.pl'].
    |
    */

    'privacy_policy' => null,

    /*
    |--------------------------------------------------------------------------
    | Language
    |--------------------------------------------------------------------------
    |
    | null = app()->getLocale(). The package ships with en and pl translations;
    | any other locale without translations falls back to 'fallback_locale'.
    |
    */

    'locale' => env('COOKIE_CONSENT_LOCALE'),

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Consent revision
    |--------------------------------------------------------------------------
    |
    | Bump it when categories or the way you process data change - every
    | visitor will be asked for consent again.
    |
    */

    'revision' => 1,

    /*
    |--------------------------------------------------------------------------
    | Cookie storing the consent
    |--------------------------------------------------------------------------
    */

    'cookie' => [
        'name' => 'cc_cookie',
        'expires_after_days' => 182,
    ],

    /*
    |--------------------------------------------------------------------------
    | Appearance
    |--------------------------------------------------------------------------
    |
    | Passed as-is to the library's guiOptions:
    | https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions
    | Colours are changed with CSS variables (--cc-btn-primary-bg etc.) in your CSS.
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
