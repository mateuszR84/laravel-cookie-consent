<?php

namespace StudioDevs\CookieConsent;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Route;
use Illuminate\Translation\Translator;

/**
 * Tłumaczy config/cookie-consent.php i tłumaczenia paczki na konfigurację
 * biblioteki orestbida/cookieconsent oraz odpowiada na pytania komponentów
 * Blade (czy baner jest potrzebny, czy dana kategoria wymaga zgody).
 */
class CookieConsent
{
    public const ASSETS = [
        'cookieconsent.umd.js' => 'dist/cookieconsent.umd.js',
        'cookieconsent.css' => 'dist/cookieconsent.css',
        'cookie-consent.js' => 'js/cookie-consent.js',
        'cookie-consent.css' => 'css/cookie-consent.css',
    ];

    public function __construct(
        private readonly Repository $config,
        private readonly Translator $translator,
        private readonly UrlGenerator $url,
    ) {}

    /**
     * Baner jest potrzebny tylko, gdy paczka jest włączona i jakaś kategoria poza niezbędnymi wymaga zgody.
     */
    public function isActive(): bool
    {
        return (bool) $this->config->get('cookie-consent.enabled') && $this->activeCategories() !== [];
    }

    /**
     * @return array<string, array{active: bool, auto_clear?: list<string>}>
     */
    public function activeCategories(): array
    {
        return array_filter(
            $this->config->get('cookie-consent.categories', []),
            fn (array $category) => (bool) ($category['active'] ?? false),
        );
    }

    /**
     * Czy treść z danej kategorii trzeba zablokować do czasu zgody.
     */
    public function requiresConsent(string $category): bool
    {
        return $this->isActive() && array_key_exists($category, $this->activeCategories());
    }

    public function googleAnalyticsId(): ?string
    {
        $id = $this->config->get('cookie-consent.google_analytics_id');

        return $id && $this->requiresConsent('analytics') ? $id : null;
    }

    public function locale(): string
    {
        $locale = $this->config->get('cookie-consent.locale') ?: $this->translator->getLocale();

        return $this->translator->hasForLocale('cookie-consent::messages.consent_modal.title', $locale)
            ? $locale
            : $this->config->get('cookie-consent.fallback_locale', 'en');
    }

    public function trans(string $key, array $replace = []): string
    {
        return $this->translator->get('cookie-consent::messages.'.$key, $replace, $this->locale());
    }

    public function categoryTitle(string $category): string
    {
        return $this->trans("categories.{$category}.title");
    }

    public function privacyPolicyUrl(): ?string
    {
        $target = $this->config->get('cookie-consent.privacy_policy');

        if (! $target) {
            return null;
        }

        return Route::has($target) ? $this->url->route($target) : $target;
    }

    /**
     * Wersjonowany URL pliku paczki - zmienia się razem z treścią pliku, więc może być cache'owany na długo.
     */
    public function assetUrl(string $file): string
    {
        return $this->url->route('cookie-consent.asset', [
            'file' => $file,
            'v' => substr(hash_file('xxh128', self::assetPath($file)), 0, 12),
        ]);
    }

    public static function assetPath(string $file): string
    {
        return dirname(__DIR__).'/resources/'.self::ASSETS[$file];
    }

    /**
     * Konfiguracja przekazywana do CookieConsent.run() (JSON w komponencie scripts).
     *
     * @return array<string, mixed>
     */
    public function jsConfig(): array
    {
        $locale = $this->locale();
        $categories = ['necessary' => ['enabled' => true, 'readOnly' => true]];
        $sections = [
            ['title' => $this->trans('preferences_modal.intro_title'), 'description' => $this->trans('preferences_modal.intro')],
            $this->section('necessary'),
        ];

        foreach ($this->activeCategories() as $name => $category) {
            $categories[$name] = array_filter([
                'autoClear' => ($category['auto_clear'] ?? []) === [] ? null : [
                    'cookies' => array_map(fn (string $cookie) => ['name' => $cookie], $category['auto_clear']),
                ],
            ]);
            $sections[] = $this->section($name);
        }

        $privacyPolicy = $this->privacyPolicyUrl();

        return [
            'revision' => (int) $this->config->get('cookie-consent.revision', 1),
            'cookie' => [
                'name' => $this->config->get('cookie-consent.cookie.name', 'cc_cookie'),
                'expiresAfterDays' => (int) $this->config->get('cookie-consent.cookie.expires_after_days', 182),
            ],
            'guiOptions' => $this->config->get('cookie-consent.gui_options', []),
            'categories' => $categories,
            'language' => [
                'default' => $locale,
                'translations' => [
                    $locale => [
                        'consentModal' => array_filter([
                            'title' => $this->trans('consent_modal.title'),
                            'description' => $this->trans('consent_modal.description'),
                            'acceptAllBtn' => $this->trans('consent_modal.accept_all'),
                            'acceptNecessaryBtn' => $this->trans('consent_modal.accept_necessary'),
                            'showPreferencesBtn' => $this->trans('consent_modal.show_preferences'),
                            'footer' => $privacyPolicy
                                ? '<a href="'.e($privacyPolicy).'">'.e($this->trans('consent_modal.privacy_policy')).'</a>'
                                : null,
                        ]),
                        'preferencesModal' => [
                            'title' => $this->trans('preferences_modal.title'),
                            'acceptAllBtn' => $this->trans('preferences_modal.accept_all'),
                            'acceptNecessaryBtn' => $this->trans('preferences_modal.accept_necessary'),
                            'savePreferencesBtn' => $this->trans('preferences_modal.save'),
                            'closeIconLabel' => $this->trans('preferences_modal.close'),
                            'sections' => $sections,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{title: string, description: string, linkedCategory: string}
     */
    private function section(string $category): array
    {
        return [
            'title' => $this->categoryTitle($category),
            'description' => $this->trans("categories.{$category}.description"),
            'linkedCategory' => $category,
        ];
    }
}
