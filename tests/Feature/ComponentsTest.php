<?php

use Illuminate\Support\Facades\Route;
use StudioDevs\CookieConsent\CookieConsent;

test('nothing is rendered when no optional category is active', function () {
    config(['cookie-consent.categories.media.active' => false]);

    expect(blade('<x-cookie-consent::styles />'))->toBe('')
        ->and(blade('<x-cookie-consent::scripts />'))->toBe('')
        ->and(blade('<x-cookie-consent::settings-link />'))->toBe('');
});

test('nothing is rendered when the package is disabled', function () {
    config([
        'cookie-consent.enabled' => false,
        'cookie-consent.categories.media.active' => true,
    ]);

    expect(blade('<x-cookie-consent::scripts />'))->toBe('')
        ->and(blade('<x-cookie-consent::embed src="https://maps.example/embed" title="Mapa" />'))
        ->toContain('<iframe src="https://maps.example/embed" title="Mapa"></iframe>');
});

test('the scripts component passes categories and translations in the configured locale', function () {
    config([
        'cookie-consent.categories.media.active' => true,
        'cookie-consent.locale' => 'pl',
        'cookie-consent.revision' => 3,
    ]);

    $html = blade('<x-cookie-consent::scripts />');

    preg_match('#<script type="application/json" id="cookie-consent-config">(.*?)</script>#s', $html, $match);
    $config = json_decode($match[1], true);

    expect($config['revision'])->toBe(3)
        ->and(array_keys($config['categories']))->toBe(['necessary', 'media'])
        ->and($config['categories']['necessary'])->toBe(['enabled' => true, 'readOnly' => true])
        ->and($config['language']['default'])->toBe('pl')
        ->and($config['language']['translations']['pl']['consentModal']['title'])->toBe('Ta strona używa plików cookies')
        ->and(array_column($config['language']['translations']['pl']['preferencesModal']['sections'], 'linkedCategory'))
        ->toBe(['necessary', 'media'])
        ->and($html)->toContain('cookie-consent/assets/cookieconsent.umd.js?v=')
        ->not->toContain('googletagmanager');
});

test('a locale without translations falls back to english', function () {
    config(['cookie-consent.categories.media.active' => true, 'cookie-consent.locale' => 'de']);

    expect(app(CookieConsent::class)->locale())->toBe('en');
});

test('the privacy policy link accepts a route name or a url', function () {
    config(['cookie-consent.categories.media.active' => true]);
    Route::get('/polityka-prywatnosci', fn () => '')->name('privacy');

    config(['cookie-consent.privacy_policy' => 'privacy']);
    expect(app(CookieConsent::class)->jsConfig()['language']['translations']['en']['consentModal']['footer'])
        ->toBe('<a href="http://localhost/polityka-prywatnosci">Privacy policy</a>');

    config(['cookie-consent.privacy_policy' => 'https://example.com/rodo']);
    expect(app(CookieConsent::class)->privacyPolicyUrl())->toBe('https://example.com/rodo');
});

test('google analytics is blocked until consent to the analytics category', function () {
    config([
        'cookie-consent.categories.analytics.active' => true,
        'cookie-consent.google_analytics_id' => 'G-TEST123',
    ]);

    $html = blade('<x-cookie-consent::scripts />');

    expect($html)
        ->toContain('<script type="text/plain" data-category="analytics" async src="https://www.googletagmanager.com/gtag/js?id=G-TEST123"></script>')
        ->toContain("gtag('config', \"G-TEST123\")")
        ->toContain('"autoClear":{"cookies":[{"name":"/^_ga/"},{"name":"_gid"}]}');
});

test('an embed in a category needing consent renders a placeholder instead of the iframe', function () {
    config(['cookie-consent.categories.media.active' => true, 'cookie-consent.locale' => 'pl']);

    $html = blade('<x-cookie-consent::embed category="media" src="https://maps.example/embed?q=a&b=c" title="Mapa" class="h-full" allowfullscreen />');

    expect($html)
        ->not->toContain('<iframe')
        ->toContain('data-cc-embed="media"')
        ->toContain('data-cc-embed-src="https://maps.example/embed?q=a&amp;b=c"')
        ->toContain('data-cc-embed-attrs="{&quot;title&quot;:&quot;Mapa&quot;,&quot;class&quot;:&quot;h-full&quot;,&quot;allowfullscreen&quot;:true}"')
        ->toContain('zezwól na kategorię „Media zewnętrzne”')
        ->toContain('data-cc-embed-accept');
});

test('an embed slot replaces the default placeholder description', function () {
    config(['cookie-consent.categories.media.active' => true]);

    $html = blade('<x-cookie-consent::embed src="https://maps.example/embed"><img src="/mapa.webp" alt="Mapa"></x-cookie-consent::embed>');

    expect($html)->toContain('<img src="/mapa.webp" alt="Mapa">')
        ->not->toContain('cc-embed__description');
});

test('the settings link is a button opening the preferences modal, with custom text and attributes', function () {
    config(['cookie-consent.categories.media.active' => true]);

    expect(blade('<x-cookie-consent::settings-link class="text-sm" />'))
        ->toContain('<button type="button" data-cc="show-preferencesModal" class="text-sm">Cookie settings</button>')
        ->and(blade('<x-cookie-consent::settings-link>Cookies</x-cookie-consent::settings-link>'))
        ->toContain('>Cookies</button>');
});

test('google analytics is not rendered when the analytics category is inactive', function () {
    config([
        'cookie-consent.categories.analytics.active' => false,
        'cookie-consent.categories.media.active' => true,
        'cookie-consent.google_analytics_id' => 'G-TEST123',
    ]);

    expect(blade('<x-cookie-consent::scripts />'))->not->toContain('G-TEST123');
});

test('the privacy policy can differ per locale', function () {
    config([
        'cookie-consent.categories.media.active' => true,
        'cookie-consent.privacy_policy' => ['pl' => '/polityka-prywatnosci', 'en' => '/en/privacy-policy'],
    ]);

    config(['cookie-consent.locale' => 'pl']);
    expect(app(CookieConsent::class)->privacyPolicyUrl())->toBe('/polityka-prywatnosci');

    config(['cookie-consent.locale' => 'en']);
    expect(app(CookieConsent::class)->privacyPolicyUrl())->toBe('/en/privacy-policy');

    config(['cookie-consent.locale' => 'de']);
    expect(app(CookieConsent::class)->privacyPolicyUrl())->toBe('/en/privacy-policy');
});
