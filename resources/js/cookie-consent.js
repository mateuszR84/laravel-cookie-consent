/*
 * Uruchamia orestbida/cookieconsent z konfiguracją wygenerowaną przez komponent
 * <x-cookie-consent::scripts> i obsługuje osadzenia <x-cookie-consent::embed>.
 */
(function () {
    'use strict';

    var configElement = document.getElementById('cookie-consent-config');

    if (!configElement || !window.CookieConsent) {
        return;
    }

    var CookieConsent = window.CookieConsent;
    var config = JSON.parse(configElement.textContent);

    // RegExp nie przechodzi przez JSON - nazwy w postaci "/.../flagi" zamieniamy z powrotem na wyrażenia regularne.
    Object.keys(config.categories).forEach(function (name) {
        var autoClear = config.categories[name].autoClear;

        if (!autoClear) {
            return;
        }

        autoClear.cookies.forEach(function (cookie) {
            var match = /^\/(.+)\/([a-z]*)$/.exec(cookie.name);

            if (match) {
                cookie.name = new RegExp(match[1], match[2]);
            }
        });
    });

    function showEmbed(container) {
        var iframe = document.createElement('iframe');
        var attributes = JSON.parse(container.getAttribute('data-cc-embed-attrs') || '{}');

        Object.keys(attributes).forEach(function (name) {
            var value = attributes[name];

            if (value === false || value === null) {
                return;
            }

            iframe.setAttribute(name, value === true ? '' : value);
        });

        iframe.src = container.getAttribute('data-cc-embed-src');
        container.querySelector('[data-cc-embed-placeholder]').hidden = true;
        container.appendChild(iframe);
    }

    function hideEmbed(container, iframe) {
        iframe.remove();
        container.querySelector('[data-cc-embed-placeholder]').hidden = false;
    }

    function updateEmbeds() {
        document.querySelectorAll('[data-cc-embed]').forEach(function (container) {
            var accepted = CookieConsent.acceptedCategory(container.getAttribute('data-cc-embed'));
            var iframe = container.querySelector('iframe');

            if (accepted && !iframe) {
                showEmbed(container);
            } else if (!accepted && iframe) {
                hideEmbed(container, iframe);
            }
        });
    }

    config.onConsent = updateEmbeds;
    config.onChange = updateEmbeds;

    // "Zezwól i pokaż" w placeholderze: dokłada kategorię osadzenia do już zaakceptowanych.
    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-cc-embed-accept]');

        if (!button) {
            return;
        }

        var category = button.closest('[data-cc-embed]').getAttribute('data-cc-embed');
        var accepted = CookieConsent.getUserPreferences().acceptedCategories || [];

        CookieConsent.acceptCategory(accepted.concat([category]));
    });

    CookieConsent.run(config);
})();
