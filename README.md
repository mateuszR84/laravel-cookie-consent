# laravel-cookie-consent

[![tests](https://github.com/mateuszR84/laravel-cookie-consent/actions/workflows/tests.yml/badge.svg)](https://github.com/mateuszR84/laravel-cookie-consent/actions/workflows/tests.yml)

A GDPR-friendly cookie consent banner for Laravel apps with Blade views. It is built on
[orestbida/cookieconsent](https://github.com/orestbida/cookieconsent) v3 (MIT) and adds the
Laravel integration around it:

- **Consent categories in `config/cookie-consent.php`.** The banner only appears **when there is
  something to ask about**: a site with no analytics and no third-party embeds gets no banner
  at all.
- **Google Analytics (GA4)** loaded only after consent to the "analytics" category.
- **`<x-cookie-consent::embed>`**: third-party iframes (Google Maps, YouTube) are inserted
  only after consent. Until then visitors see a placeholder with an "Allow and show" button.
- **English and Polish translations** included.
- **JS/CSS served straight from `vendor/`** with versioned URLs and a one-year cache. There is
  nothing to publish to `public/`, no Vite step, and no requests to a third-party CDN.

Requires PHP 8.3+ and Laravel 12 or 13.

## Installation

The package isn't on Packagist yet. Add the repository to your app's `composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/mateuszR84/laravel-cookie-consent" }
]
```

```bash
composer require studiodevs/laravel-cookie-consent
php artisan vendor:publish --tag=cookie-consent-config
```

In your layout:

```blade
<head>
    ...
    <x-cookie-consent::styles />
</head>
<body>
    ...
    <footer>
        <x-cookie-consent::settings-link class="..." />
    </footer>

    <x-cookie-consent::scripts />
</body>
```

All three components render nothing when no category requires consent.

## Configuration

| Key | Description |
| --- | --- |
| `enabled` | Master switch (`COOKIE_CONSENT_ENABLED`). |
| `categories.<name>.active` | Whether the category requires consent. `analytics` switches on by itself when `GOOGLE_ANALYTICS_ID` is set; `media` (`COOKIE_CONSENT_MEDIA`) and `marketing` (`COOKIE_CONSENT_MARKETING`) are off by default. |
| `categories.<name>.auto_clear` | Cookies erased when consent is withdrawn; `"/^_ga/"` is a regular expression. |
| `google_analytics_id` | GA4 measurement ID (`GOOGLE_ANALYTICS_ID`). |
| `privacy_policy` | Route name or URL of your privacy policy, linked in the banner footer. On a multilingual site, an array keyed by locale: `['en' => 'privacy', 'pl' => 'privacy.pl']`. |
| `locale` | Banner language (`COOKIE_CONSENT_LOCALE`); `null` = the app locale, and a locale without translations uses `fallback_locale`. |
| `revision` | Bump it to ask every visitor for consent again (e.g. after adding a category). |
| `cookie` | Name and lifetime in days of the cookie storing the consent. |
| `gui_options` | Passed as-is to the library's [`guiOptions`](https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions). |

**Custom category:** add a key under `categories` and translations for
`categories.<name>.title` / `.description` (`php artisan vendor:publish --tag=cookie-consent-lang`).

**Custom scripts after consent:** the library runs any
`<script type="text/plain" data-category="<name>">` only once the user consents to that category.

**Other languages:** publish the translations and add `lang/vendor/cookie-consent/<locale>/messages.php`.

## Third-party embeds

```blade
<x-cookie-consent::embed category="media"
    src="https://www.google.com/maps?q=...&output=embed"
    title="Map"
    class="h-full w-full"
    loading="lazy"
    allowfullscreen />
```

Every attribute except `category` and `src` goes onto the iframe. The slot replaces the
default placeholder text, e.g. with a static map image. When the category requires no consent,
the component renders a plain iframe.

## Styling

- **Banner and preferences modal:** the library's CSS variables on `#cc-main`, e.g.
  `#cc-main { --cc-btn-primary-bg: #cc1f1f; --cc-font-family: inherit; }`
  (see the full list in the [docs](https://cookieconsent.orestbida.com/advanced/ui-customization.html)).
  For a dark theme, add the `cc--darkmode` class to `<html>`.
- **Embed placeholder:** `--cc-embed-bg`, `--cc-embed-color`, `--cc-embed-button-bg`,
  `--cc-embed-button-color`, `--cc-embed-button-hover-bg`, `--cc-embed-button-radius`.
- **Custom markup:** `php artisan vendor:publish --tag=cookie-consent-views`.

## Updating orestbida/cookieconsent

`dist/cookieconsent.umd.js` and `dist/cookieconsent.css` from the `vanilla-cookieconsent` npm
package live in `resources/dist/` (currently v3.1.0; licence in
`resources/dist/LICENSE-cookieconsent`). After swapping them, the URLs change by themselves,
because the `?v=` value is a hash of the file contents.

## Testing

```bash
composer install
vendor/bin/pest
vendor/bin/pint --test
```

## Licence

MIT
