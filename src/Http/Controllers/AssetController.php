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
     * Files are served straight from vendor/, so nothing has to be published to public/ after each package
     * update. URLs rendered by the components carry ?v=<content hash>, which makes a one-year cache safe.
     */
    public function __invoke(string $file): BinaryFileResponse
    {
        return response()->file(CookieConsent::assetPath($file), [
            'Content-Type' => self::CONTENT_TYPES[pathinfo($file, PATHINFO_EXTENSION)],
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
