<?php

namespace StudioDevs\CookieConsent\Http\Controllers;

use StudioDevs\CookieConsent\CookieConsent;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController
{
    private const CONTENT_TYPES = [
        'js' => 'application/javascript; charset=UTF-8',
        'css' => 'text/css; charset=UTF-8',
    ];

    /**
     * Pliki są serwowane prosto z vendor/, więc nie trzeba ich publikować do public/ po każdej aktualizacji
     * paczki. URL-e z komponentów mają ?v=<hash treści>, dlatego cache może być roczny.
     */
    public function __invoke(string $file): BinaryFileResponse
    {
        return response()->file(CookieConsent::assetPath($file), [
            'Content-Type' => self::CONTENT_TYPES[pathinfo($file, PATHINFO_EXTENSION)],
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
