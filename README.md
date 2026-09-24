# laravel-cookie-consent

Baner zgody na cookies dla aplikacji Laravel z widokami Blade. Pod spodem jest
[orestbida/cookieconsent](https://github.com/orestbida/cookieconsent) v3 (MIT), a paczka
dokłada integrację z Laravelem:

- kategorie zgód w `config/cookie-consent.php`; baner pojawia się **tylko wtedy, gdy jest
  na co pytać** (strona bez analityki i bez osadzeń z zewnątrz nie dostaje banera),
- Google Analytics (GA4) ładowany dopiero po zgodzie na kategorię „analytics”,
- `<x-cookie-consent::embed>`: iframe z zewnątrz (Google Maps, YouTube) wstawiany dopiero po
  zgodzie, do tego czasu placeholder z przyciskiem „Zezwól i pokaż”,
- tłumaczenia pl i en,
- pliki JS i CSS serwowane z `vendor/` z wersjonowanym URL-em i rocznym cache. Nie trzeba nic
  publikować do `public/` ani budować przez Vite, a do zewnętrznego CDN nie idą żadne zapytania.

Wymaga PHP 8.3+ i Laravela 12 albo 13.

## Instalacja

Paczka nie jest na Packagist. Dodaj repozytorium do `composer.json` projektu:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/mateuszR84/laravel-cookie-consent" }
]
```

```bash
composer require studiodevs/laravel-cookie-consent
php artisan vendor:publish --tag=cookie-consent-config
```

W layoucie:

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

Wszystkie trzy komponenty nic nie renderują, gdy żadna kategoria nie wymaga zgody.

## Konfiguracja

| Klucz | Opis |
| --- | --- |
| `enabled` | Główny włącznik (`COOKIE_CONSENT_ENABLED`). |
| `categories.<nazwa>.active` | Czy kategoria wymaga zgody. `analytics` włącza się sam, gdy jest ustawione `GOOGLE_ANALYTICS_ID`; `media` (`COOKIE_CONSENT_MEDIA`) i `marketing` (`COOKIE_CONSENT_MARKETING`) są domyślnie wyłączone. |
| `categories.<nazwa>.auto_clear` | Cookies kasowane po wycofaniu zgody; `"/^_ga/"` to wyrażenie regularne. |
| `google_analytics_id` | ID GA4 (`GOOGLE_ANALYTICS_ID`). |
| `privacy_policy` | Nazwa trasy albo URL polityki prywatności; link trafia do stopki banera. Dla strony wielojęzycznej tablica per język: `['pl' => 'privacy.show', 'en' => 'privacy.show.en']`. |
| `locale` | Język banera (`COOKIE_CONSENT_LOCALE`); `null` = język aplikacji, brak tłumaczenia = `fallback_locale`. |
| `revision` | Podbij, żeby wszyscy zobaczyli baner ponownie (np. po dodaniu kategorii). |
| `cookie` | Nazwa cookie ze zgodą i czas ważności w dniach. |
| `gui_options` | Przekazywane wprost do [`guiOptions`](https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions) biblioteki. |

**Własna kategoria:** dodaj klucz w `categories` i tłumaczenie
`categories.<nazwa>.title` / `.description` (`php artisan vendor:publish --tag=cookie-consent-lang`).

**Własne skrypty po zgodzie:** biblioteka uruchamia każdy
`<script type="text/plain" data-category="<nazwa>">` dopiero po zgodzie na tę kategorię.

## Osadzenia z zewnątrz

```blade
<x-cookie-consent::embed category="media"
    src="https://www.google.com/maps?q=...&output=embed"
    title="Mapa dojazdu"
    class="h-full w-full"
    loading="lazy"
    allowfullscreen />
```

Wszystkie atrybuty poza `category` i `src` trafiają na iframe. Slot zastępuje domyślny opis
w placeholderze, np. statycznym obrazkiem mapy. Gdy kategoria nie wymaga zgody, komponent
renderuje zwykły iframe.

## Wygląd

- **Baner i okno ustawień:** zmienne CSS biblioteki na `#cc-main`, np.
  `#cc-main { --cc-btn-primary-bg: #cc1f1f; --cc-font-family: inherit; }`
  (pełna lista w [dokumentacji](https://cookieconsent.orestbida.com/advanced/ui-customization.html)).
- **Placeholder osadzeń:** `--cc-embed-bg`, `--cc-embed-color`, `--cc-embed-button-bg`,
  `--cc-embed-button-color`, `--cc-embed-button-hover-bg`, `--cc-embed-button-radius`.
- **Całkowita zmiana markupu:** `php artisan vendor:publish --tag=cookie-consent-views`.

## Aktualizacja biblioteki orestbida

Pliki `dist/cookieconsent.umd.js` i `dist/cookieconsent.css` z paczki npm
`vanilla-cookieconsent` leżą w `resources/dist/` (obecnie v3.1.0, licencja w
`resources/dist/LICENSE-cookieconsent`). Po podmianie URL-e zmienią się same, bo wersja w
`?v=` to hash treści pliku.

## Testy

```bash
composer install
vendor/bin/pest
vendor/bin/pint --test
```

## Licencja

MIT
