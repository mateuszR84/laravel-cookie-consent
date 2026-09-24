<?php

test('package assets are served with long cache headers', function (string $file, string $contentType) {
    $response = $this->get("/cookie-consent/assets/{$file}?v=abc");

    $response->assertOk()
        ->assertHeader('Content-Type', $contentType);

    expect($response->headers->get('Cache-Control'))->toContain('max-age=31536000')
        ->and($response->headers->getCookies())->toBe([]);
})->with([
    ['cookieconsent.umd.js', 'application/javascript; charset=UTF-8'],
    ['cookie-consent.js', 'application/javascript; charset=UTF-8'],
    ['cookieconsent.css', 'text/css; charset=UTF-8'],
    ['cookie-consent.css', 'text/css; charset=UTF-8'],
]);

test('only known assets can be requested', function () {
    $this->get('/cookie-consent/assets/..%2F..%2Fcomposer.json')->assertNotFound();
    $this->get('/cookie-consent/assets/other.js')->assertNotFound();
});
